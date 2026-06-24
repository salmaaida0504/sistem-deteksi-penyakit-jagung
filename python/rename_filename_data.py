import os
import zipfile

dataset_dir = r"E:\SKRIPSI\plantdiseasdataset_corn_ori_process_pict"
output_zip  = r"E:\SKRIPSI\plantdiseasdataset_corn_ori_process_pict_fixed.zip"

# ── Step 1: Rename semua file yang punya trailing whitespace ──────────────────
print("=== Step 1: Renaming files ===")
renamed_count = 0

for root, dirs, files in os.walk(dataset_dir):
    for filename in files:
        name, ext = os.path.splitext(filename)
        new_name = name.rstrip() + ext          # hapus spasi di akhir nama

        if new_name != filename:
            old_path = os.path.join(root, filename)
            new_path = os.path.join(root, new_name)
            os.rename(old_path, new_path)
            print(f"  Renamed: '{filename}'  →  '{new_name}'")
            renamed_count += 1

print(f"\nTotal renamed: {renamed_count} file(s)\n")

# ── Step 2: Zip ulang seluruh dataset ────────────────────────────────────────
print("=== Step 2: Creating ZIP ===")

with zipfile.ZipFile(output_zip, 'w', zipfile.ZIP_DEFLATED) as zf:
    for root, dirs, files in os.walk(dataset_dir):
        for filename in files:
            file_path    = os.path.join(root, filename)
            # Path di dalam ZIP relatif terhadap parent folder SKRIPSI
            arcname      = os.path.relpath(file_path, os.path.dirname(dataset_dir))
            zf.write(file_path, arcname)

print(f"\nZIP berhasil dibuat di:\n  {output_zip}")
