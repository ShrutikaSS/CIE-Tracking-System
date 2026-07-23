import os
import sys

sys.stdout.reconfigure(encoding='utf-8')
root_dir = r"c:\Users\dukar\OneDrive\Attachments\문서"

def main():
    print("Listing all files in 문서 recursively...")
    count = 0
    for root, dirs, files in os.walk(root_dir):
        # Skip git folders and system directories
        if '.git' in dirs:
            dirs.remove('.git')
        for file in files:
            path = os.path.join(root, file)
            rel_path = os.path.relpath(path, root_dir)
            name_lower = file.lower()
            if any(term in name_lower for term in ['srs', 'requirement', 'cie', 'spec', 'case', 'mark', 'track']):
                print(f"Match: {rel_path}")
                count += 1
    print(f"Total matching files found: {count}")

if __name__ == "__main__":
    main()
