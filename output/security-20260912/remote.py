import os
import pathlib
import subprocess
import sys

base = pathlib.Path(__file__).resolve().parent
ssh = ['ssh', '-i', str(pathlib.Path.home()/'.ssh/codesblock_hostinger_20260912'), '-p', '65002', '-o', 'BatchMode=yes', '-o', 'ConnectTimeout=15', 'u955006780@46.202.161.55']
script = pathlib.Path(sys.argv[1])
result = subprocess.run(ssh + ['bash -s'], input=script.read_bytes().replace(b'\r\n', b'\n'), stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
(base/(script.stem+'.log')).write_bytes(result.stdout)
print(result.stdout.decode('utf-8', errors='replace'))
sys.exit(result.returncode)
