from pathlib import Path
import shutil

base = Path(__file__).resolve().parent
local = base.parents[1]/'app/public/wp-content'
def files(root):
    return {p.relative_to(root):p for p in root.rglob('*') if p.is_file() and '.git' not in p.relative_to(root).parts}
def compare(a,b):
    af,bf=files(a),files(b)
    changes=[]
    for p in sorted(af.keys()|bf.keys()):
        if p not in af or p not in bf or af[p].read_bytes().replace(b'\r\n',b'\n') != bf[p].read_bytes().replace(b'\r\n',b'\n'):
            changes.append(str(p))
    return changes
for name in ['codesblock-core','codesblock-commerce']:
    print(name, compare(local/'plugins'/name, base/'wp-content/plugins'/name))
print('Local vs restored live theme:', compare(local/'themes/codesblock',base/'live-theme'))
for rel,p in files(base/'live-theme').items():
    dest=base/'deploy-repo'/rel
    dest.parent.mkdir(parents=True,exist_ok=True)
    shutil.copy2(p,dest)
