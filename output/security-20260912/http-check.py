import urllib.request, urllib.error, json
from pathlib import Path
paths=['/','/articles/','/courses/','/wp-json/','/.git/config','/wp-config.php','/codesblock-io-20260902-v2.sql','/wp-content/uploads/mailpoet/index.php','/wp-json/wp/v2/users','/wp-json/wp/v2/course_lesson']
rows=[]
for path in paths:
    try:
        r=urllib.request.urlopen('https://codesblock.io'+path,timeout=20)
    except urllib.error.HTTPError as e:
        r=e
    body=r.read()
    row=dict(path=path,status=r.status,type=r.headers.get('Content-Type'),bytes=len(body))
    if path=='/': row['headers']=dict(r.headers)
    if path.startswith('/wp-json/wp/v2/'):
        try: row['body']=json.loads(body)
        except ValueError: pass
    rows.append(row)
    print(path,r.status,row['type'],len(body))
Path(__file__).with_suffix('.json').write_text(json.dumps(rows,indent=2))
