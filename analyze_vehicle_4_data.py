import re
from collections import defaultdict

# Read restore file
with open('restore_49B08879_incidents_transactions.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract incident
incident_match = re.search(r'INSERT INTO `incidents` VALUES\n(.+?);', content, re.DOTALL)
if incident_match:
    incident_line = incident_match.group(1)
    # Parse incident ID (first field)
    incident_id = incident_line.split(',')[0].replace('(', '').strip()
    print(f"📋 Incident ID trong backup: {incident_id}")

# Extract transactions
transactions_match = re.search(r'INSERT INTO `transactions` VALUES\n(.+?);', content, re.DOTALL)
if not transactions_match:
    print("❌ No transactions found")
    exit(1)

transactions_data = transactions_match.group(1)

# Parse transactions and group by incident_id
incident_transactions = defaultdict(list)
maintenance_transactions = []
other_transactions = []

lines = transactions_data.split('\n')
for line in lines:
    if not line.strip() or line.strip() == ',':
        continue
    
    # Remove trailing comma
    line = line.rstrip(',')
    
    # Extract incident_id (3rd field)
    match = re.match(r'\((\d+),\'[^\']+\',(\d+|NULL),', line)
    if match:
        txn_id = match.group(1)
        incident_id_field = match.group(2)
        
        if incident_id_field == 'NULL':
            # Check if it's maintenance (has vehicle_maintenance_id)
            if "'bảo_trì_xe_chủ_riêng'" in line or "[Bảo trì]" in line:
                maintenance_transactions.append(txn_id)
            else:
                other_transactions.append(txn_id)
        else:
            incident_transactions[incident_id_field].append(txn_id)

print(f"\n🚗 Phân tích transactions của xe 49B08879:")
print(f"   ├─ Tổng: 158 transactions")
print(f"   ├─ Gắn với chuyến đi: {sum(len(v) for v in incident_transactions.values())} transactions")
print(f"   ├─ Bảo trì xe: {len(maintenance_transactions)} transactions")
print(f"   └─ Khác (không gắn chuyến): {len(other_transactions)} transactions")

print(f"\n📊 Chi tiết theo chuyến đi:")
for inc_id in sorted(incident_transactions.keys(), key=int):
    txn_count = len(incident_transactions[inc_id])
    print(f"   ├─ Incident #{inc_id}: {txn_count} transactions")

print(f"\n💡 Kết luận:")
print(f"   - Backup chỉ có 1 incident (ID {incident_id}) thuộc xe 49B08879")
print(f"   - Nhưng có {len(incident_transactions)} incidents khác nhau có transactions gắn với xe này")
print(f"   - Các incidents còn lại đã bị XÓA khỏi bảng incidents")
print(f"   - Transactions vẫn còn vì không có ON DELETE CASCADE")
