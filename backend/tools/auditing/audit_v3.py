import os
import yaml
from collections import Counter

def audit_v3():
    config_path = 'backend/multi_currency_gpu_v3/data.yaml'
    with open(config_path, 'r') as f:
        config = yaml.safe_load(f)
    
    names = config['names']
    counts = Counter()
    
    for split in ['train', 'val']:
        label_dir = os.path.join('backend/multi_currency_gpu_v3', 'labels', split)
        for file in os.listdir(label_dir):
            if file.endswith('.txt'):
                with open(os.path.join(label_dir, file), 'r') as f:
                    for line in f:
                        parts = line.split()
                        if parts:
                            counts[int(parts[0])] += 1
                            
    print("-" * 50)
    print(f"{'ID':<5} {'Name':<20} {'Count':<10}")
    print("-" * 50)
    for i in range(len(names)):
        print(f"{i:<5} {names[i]:<20} {counts[i]:<10}")
    print("-" * 50)
    print(f"Total Labels: {sum(counts.values())}")

if __name__ == "__main__":
    audit_v3()
