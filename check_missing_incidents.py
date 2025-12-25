import re

# Read SQL file
with open('database/backup_before_clean_49B08879_20251225_012250.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Target incident IDs from analysis
target_ids = [7, 28, 42, 45, 46, 49, 51, 54, 56, 58, 63, 66, 71, 78, 80, 81, 84, 85, 88, 93, 94, 95, 98, 99, 100, 106, 123, 124, 136, 138]

# Extract incidents
incidents_match = re.search(r'INSERT INTO `incidents` VALUES (.+?);', content, re.DOTALL)
incidents_data = incidents_match.group(1)

# Find all incidents
found_incidents = {}
for target_id in target_ids:
    # Look for pattern (ID, at start of row
    pattern = rf'\({target_id},'
    if pattern in incidents_data:
        # Extract the full row
        start = incidents_data.find(f'({target_id},')
        if start != -1:
            end = incidents_data.find('),', start)
            if end == -1:
                end = incidents_data.find(');', start)
            if end != -1:
                row = incidents_data[start:end+1]
                found_incidents[target_id] = row

print(f"🔍 Tìm thấy {len(found_incidents)}/{len(target_ids)} incidents trong backup")
print(f"   Tìm thấy: {sorted(found_incidents.keys())}")
print(f"   Thiếu: {sorted(set(target_ids) - set(found_incidents.keys()))}")

if len(found_incidents) < len(target_ids):
    print(f"\n⚠️  Thiếu {len(target_ids) - len(found_incidents)} incidents!")
    print("   Các incidents này đã bị xóa TRƯỚC KHI backup được tạo")
