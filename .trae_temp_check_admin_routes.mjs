import fs from 'fs';

const view = fs.readFileSync('resources/views/admin/dashboard.blade.php', 'utf8');
const routes = JSON.parse(fs.readFileSync('routes_list.json', 'utf8'));
const names = new Set(routes.map((route) => route.name).filter(Boolean));
const matches = [...view.matchAll(/route\('([^']+)'/g)].map((match) => match[1]);
const uniqueMatches = [...new Set(matches)];
const missing = uniqueMatches.filter((name) => !names.has(name)).sort();

console.log(`TOTAL ${uniqueMatches.length}`);
console.log(`MISSING ${missing.length}`);

for (const name of missing) {
    console.log(name);
}
