<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Contract;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DisputeInitiated;

class DisputeController extends Controller
{
    public function index()
    {
        $disputes = Dispute::with(['initiator', 'respondent', 'disputable', 'mediator'])
            ->when(!Auth::user()->isAdmin(), function($query) {
                return $query->where(function($q) {
                    $q->where('initiator_id', Auth::id())
                      ->orWhere('respondent_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('disputes.index', compact('disputes'));
    }

    public function create($type, $id)
    {
        $disputable = $this->getDisputableModel($type, $id);
        
        if (!$disputable) {
            abort(404);
        }

        // Check if user can initiate dispute for this item
        if (!$this->canInitiateDispute($disputable)) {
            abort(403, 'غير مصرح لك ببدء نزاع لهذا العنصر');
        }

        $disputeTypes = $this->getDisputeTypes();
        $resolutionMethods = $this->getResolutionMethods();

        return view('disputes.create', compact('disputable', 'type', 'disputeTypes', 'resolutionMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'disputable_type' => 'required|string|in:contract,property',
            'disputable_id' => 'required|integer',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'dispute_amount' => 'nullable|numeric|min:0',
            'desired_outcome' => 'required|string|min:20',
            'evidence_description' => 'nullable|string|max:1000',
            'preferred_resolution_method' => 'required|string',
            'willing_to_mediate' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            $disputable = $this->getDisputableModel($request->disputable_type, $request->disputable_id);
            $respondent = $this->getRespondent($disputable);

            $dispute = Dispute::create([
                'initiator_id' => Auth::id(),
                'respondent_id' => $respondent->id,
                'disputable_type' => get_class($disputable),
                'disputable_id' => $disputable->id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'dispute_amount' => $request->dispute_amount,
                'desired_outcome' => $request->desired_outcome,
                'evidence_description' => $request->evidence_description,
                'preferred_resolution_method' => $request->preferred_resolution_method,
                'willing_to_mediate' => $request->has('willing_to_mediate'),
                'status' => 'pending',
                'reference_number' => $this->generateReferenceNumber()
            ]);

            // Handle evidence files
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('disputes/evidence', $fileName, 'public');
                    
                    $dispute->evidenceFiles()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::id()
                    ]);
                }
            }

            // Auto-assign mediator if requested
            if ($request->has('willing_to_mediate')) {
                $this->assignMediator($dispute);
            }

            // Send notifications
            $this->sendNotifications($dispute);

            DB::commit();

            return redirect()->route('disputes.show', $dispute->id)
                ->with('success', 'تم بدء النزاع بنجاح. رقم المرجع: ' . $dispute->reference_number);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء بدء النزاع: ' . $e->getMessage());
        }
    }

    public function show(Dispute $dispute)
    {
        if ($dispute->initiator_id !== Auth::id() && 
            $dispute->respondent_id !== Auth::id() && 
            !Auth::user()->isAdmin() &&
            $dispute->mediator_id !== Auth::id()) {
            abort(403);
        }

        $dispute->load(['initiator', 'respondent', 'disputable', 'mediator', 'evidenceFiles', 'responses.user']);

        return view('disputes.show', compact('dispute'));
    }

    public function respond(Request $request, Dispute $dispute)
    {
        if ($dispute->respondent_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'response_type' => 'required|in:accept,reject,negotiate',
            'message' => 'required|string|min:20',
            'counter_offer' => 'nullable|string|min:20',
            'evidence_description' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        
        try {
            $dispute->responses()->create([
                'user_id' => Auth::id(),
                'response_type' => $request->response_type,
                'message' => $request->message,
                'counter_offer' => $request->counter_offer,
                'evidence_description' => $request->evidence_description
            ]);

            // Handle response evidence files
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('disputes/evidence', $fileName, 'public');
                    
                    $dispute->evidenceFiles()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::id()
                    ]);
                }
            }

            // Update dispute status based on response
            if ($request->response_type === 'accept') {
                $dispute->update([
                    'status' => 'resolved',
                    'resolution_method' => 'agreement',
                    'resolved_at' => now()
                ]);
            } elseif ($request->response_type === 'negotiate') {
                $dispute->update(['status' => 'negotiation']);
            } else {
                $dispute->update(['status' => 'contested']);
            }

            // Update last activity
            $dispute->update(['last_activity_at' => now()]);

            // Notify other party
            $this->notifyResponse($dispute, $request->response_type);

            DB::commit();

            return back()->with('success', 'تم إضافة الرد بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الرد: ' . $e->getMessage());
        }
    }

    public function assignMediator(Dispute $dispute)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'mediator_id' => 'required|exists:users,id'
        ]);

        $dispute->update([
            'mediator_id' => $request->mediator_id,
            'mediation_started_at' => now(),
            'status' => 'mediation'
        ]);

        // Notify mediator
        $dispute->mediator->notifications()->create([
            'type' => 'dispute_mediation_assigned',
            'title' => 'تعيين كوسيط في نزاع',
            'message' => "تم تعيينك كوسيط في النزاع: {$dispute->title}",
            'data' => ['dispute_id' => $dispute->id]
        ]);

        return back()->with('success', 'تم تعيين الوسيط بنجاح');
    }

    public function escalate(Dispute $dispute)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $dispute->update([
            'status' => 'escalated',
            'escalated_at' => now()
        ]);

        // Notify legal team
        User::where('role', 'legal')->get()->each(function($user) use ($dispute) {
            $user->notifications()->create([
                'type' => 'dispute_escalated',
                'title' => 'نزاع مرفوع',
                'message' => "تم رفع النزاع: {$dispute->title}",
                'data' => ['dispute_id' => $dispute->id]
            ]);
        });

        return back()->with('success', 'تم رفع النزاع بنجاح');
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        if (!Auth::user()->isAdmin() && $dispute->mediator_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'resolution_details' => 'required|string|min:50',
            'resolution_amount' => 'nullable|numeric|min:0',
            'agreement_terms' => 'nullable|string|min:20'
        ]);

        $dispute->update([
            'status' => 'resolved',
            'resolution_method' => $request->resolution_method ?? 'mediation',
            'resolution_details' => $request->resolution_details,
            'resolution_amount' => $request->resolution_amount,
            'agreement_terms' => $request->agreement_terms,
            'resolved_at' => now()
        ]);

        // Notify both parties
        $this->notifyResolution($dispute);

        return back()->with('success', 'تم حل النزاع بنجاح');
    }

    public function close(Dispute $dispute)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $dispute->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        return back()->with('success', 'تم إغلاق النزاع');
    }

    public function myDisputes()
    {
        $disputes = Dispute::with(['respondent', 'disputable'])
            ->where('initiator_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('disputes.my-disputes', compact('disputes'));
    }

    public function getStatistics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total' => Dispute::count(),
            'pending' => Dispute::where('status', 'pending')->count(),
            'negotiation' => Dispute::where('status', 'negotiation')->count(),
            'mediation' => Dispute::where('status', 'mediation')->count(),
            'resolved' => Dispute::where('status', 'resolved')->count(),
            'escalated' => Dispute::where('status', 'escalated')->count(),
            'by_type' => Dispute::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'resolution_methods' => Dispute::whereNotNull('resolution_method')
                ->selectRaw('resolution_method, count(*) as count')
                ->groupBy('resolution_method')
                ->get(),
            'avg_resolution_time' => Dispute::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, resolved_at)) as avg_days')
                ->first()
        ];

        return response()->json($stats);
    }

    private function getDisputableModel($type, $id)
    {
        switch ($type) {
            case 'contract':
                return Contract::findOrFail($id);
            case 'property':
                return Property::findOrFail($id);
            default:
                return null;
        }
    }

    private function canInitiateDispute($disputable)
    {
        $user = Auth::user();

        if ($disputable instanceof Contract) {
            return $disputable->buyer_id === $user->id || $disputable->seller_id === $user->id;
        }

        if ($disputable instanceof Property) {
            return $disputable->user_id === $user->id || 
                   $disputable->offers()->where('buyer_id', $user->id)->exists();
        }

        return false;
    }

    private function getRespondent($disputable)
    {
        $user = Auth::user();

        if ($disputable instanceof Contract) {
            return $disputable->buyer_id === $user->id ? $disputable->seller : $disputable->buyer;
        }

        if ($disputable instanceof Property) {
            return $disputable->user;
        }

        return null;
    }

    private function getDisputeTypes()
    {
        return [
            'breach_of_contract' => 'خرق العقد',
            'payment_dispute' => 'نزاع دفع',
            'property_condition' => 'حالة العقار',
            'service_quality' => 'جودة الخدمة',
            'misrepresentation' => 'معلومات مضللة',
            'timeline_delays' => 'تأخيرات في الجدول الزمني',
            'deposit_return' => 'استرداد العربون',
            'other' => 'أخرى'
        ];
    }

    private function getResolutionMethods()
    {
        return [
            'negotiation' => 'تفاوض مباشر',
            'mediation' => 'وساطة',
            'arbitration' => 'تحكيم',
            'legal_action' => 'إجراء قانوني',
            'settlement' => 'تسوية'
        ];
    }

    private function generateReferenceNumber()
    {
        return 'DSP-' . date('Y') . '-' . str_pad(Dispute::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    private function assignMediator(Dispute $dispute)
    {
        $mediator = User::where('role', 'mediator')
            ->where('is_active', true)
            ->orderBy('dispute_count', 'asc')
            ->first();

        if ($mediator) {
            $dispute->update([
                'mediator_id' => $mediator->id,
                'mediation_started_at' => now(),
                'status' => 'mediation'
            ]);

            $mediator->increment('dispute_count');
        }
    }

    private function sendNotifications(Dispute $dispute)
    {
        // Notify respondent
        $dispute->respondent->notifications()->create([
            'type' => 'dispute_initiated',
            'title' => 'نزاع جديد ضدك',
            'message' => "تم بدء نزاع جديد: {$dispute->title}",
            'data' => ['dispute_id' => $dispute->id]
        ]);

        // Send email
        Mail::to($dispute->respondent->email)->send(new DisputeInitiated($dispute));
    }

    private function notifyResponse(Dispute $dispute, $responseType)
    {
        $otherParty = $dispute->initiator_id === Auth::id() ? $dispute->respondent : $dispute->initiator;
        
        $otherParty->notifications()->create([
            'type' => 'dispute_response',
            'title' => 'رد على النزاع',
            'message' => "تم {$this->getResponseTypeText($responseType)} النزاع",
            'data' => ['dispute_id' => $dispute->id]
        ]);
    }

    private function notifyResolution(Dispute $dispute)
    {
        // Notify both parties
        $dispute->initiator->notifications()->create([
            'type' => 'dispute_resolved',
            'title' => 'حل النزاع',
            'message' => 'تم حل النزاع بنجاح',
            'data' => ['dispute_id' => $dispute->id]
        ]);

        $dispute->respondent->notifications()->create([
            'type' => 'dispute_resolved',
            'title' => 'حل النزاع',
            'message' => 'تم حل النزاع بنجاح',
            'data' => ['dispute_id' => $dispute->id]
        ]);
    }

    private function getResponseTypeText($type)
    {
        $texts = [
            'accept' => 'قبول',
            'reject' => 'رفض',
            'negotiate' => 'التفاوض على'
        ];

        return $texts[$type] ?? $type;
    }
}
