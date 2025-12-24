import re

# Read SQL file
with open('database/backup_before_clean_49B08879_20251225_012250.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract full incidents line
incidents_match = re.search(r'INSERT INTO `incidents` VALUES (.+?);', content, re.DOTALL)
if not incidents_match:
    print("❌ No incidents found")
    exit(1)

incidents_line = incidents_match.group(1)

# Split by ),( pattern
parts = incidents_line.split('),(')
parts[0] = parts[0].lstrip('(')
parts[-1] = parts[-1].rstrip(')')

print(f"📋 Found {len(parts)} total incidents in backup")

# Parse each incident to find vehicle_id=4
vehicle_4_incidents = []

for part in parts:
    # Split by comma to get fields
    # Structure: id, vehicle_id, patient_id, ...
    fields = part.split(',')
    if len(fields) >= 2:
        incident_id = fields[0]
        vehicle_id = fields[1]
        
        if vehicle_id.strip() == '4':
            vehicle_4_incidents.append(f"({part})")
            print(f"   ✓ Incident #{incident_id}: vehicle_id=4")

print(f"\n🚗 Tìm thấy {len(vehicle_4_incidents)} chuyến đi của xe 49B08879")

# Extract transactions for vehicle_id=4
transactions_match = re.search(r'INSERT INTO `transactions` VALUES (.+?);', content, re.DOTALL)
if not transactions_match:
    print("❌ No transactions found")
    exit(1)

transactions_line = transactions_match.group(1)
parts = transactions_line.split('),(')
parts[0] = parts[0].lstrip('(')
parts[-1] = parts[-1].rstrip(')')

vehicle_4_transactions = []
for part in parts:
    # Transaction structure: id, code, incident_id, staff_id, vehicle_id, ...
    # vehicle_id is 5th field (index 4)
    fields = part.split(',')
    if len(fields) >= 5 and fields[4].strip() == '4':
        vehicle_4_transactions.append(f"({part})")

print(f"💰 Found {len(vehicle_4_transactions)} transactions for vehicle 49B08879")

# Create complete restore file
restore_sql = """-- Restore ALL incidents and transactions for vehicle 49B08879
-- Generated from backup_before_clean_49B08879_20251225_012250.sql

SET FOREIGN_KEY_CHECKS=0;

"""

# Add incidents
if vehicle_4_incidents:
    restore_sql += f"-- Restore {len(vehicle_4_incidents)} incidents\n"
    restore_sql += "INSERT INTO `incidents` VALUES\n"
    restore_sql += ",\n".join(vehicle_4_incidents)
    restore_sql += ";\n\n"

# Add transactions
if vehicle_4_transactions:
    restore_sql += f"-- Restore {len(vehicle_4_transactions)} transactions\n"
    restore_sql += "INSERT INTO `transactions` VALUES\n"
    restore_sql += ",\n".join(vehicle_4_transactions)
    restore_sql += ";\n\n"

restore_sql += "SET FOREIGN_KEY_CHECKS=1;\n"

# Save to file
output_file = 'restore_49B08879_incidents_transactions.sql'
with open(output_file, 'w', encoding='utf-8') as f:
    f.write(restore_sql)

print(f"\n✅ Created restore file: {output_file}")
print(f"   - {len(vehicle_4_incidents)} incidents")
print(f"   - {len(vehicle_4_transactions)} transactions")
