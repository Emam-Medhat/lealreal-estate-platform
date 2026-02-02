import os
import re

migrations_path = r'f:\larvel state big\database\migrations'
table_creations = {}

if not os.path.exists(migrations_path):
    print(f"Path not found: {migrations_path}")
    exit(1)

for filename in os.listdir(migrations_path):
    if filename.endswith('.php'):
        file_path = os.path.join(migrations_path, filename)
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                matches = re.findall(r"Schema::create\(['\"]([^'\"]+)['\"]", content)
                for table in matches:
                    if table not in table_creations:
                        table_creations[table] = []
                    table_creations[table].append(filename)
        except Exception as e:
            print(f"Error reading {filename}: {e}")

duplicates = {table: files for table, files in table_creations.items() if len(files) > 1}

if not duplicates:
    print("No duplicate table creations found.")
else:
    for table, files in duplicates.items():
        print(f"Table '{table}' is created in:")
        for file in files:
            print(f"  - {file}")
