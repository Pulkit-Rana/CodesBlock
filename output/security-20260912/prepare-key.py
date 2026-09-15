import os
import subprocess

key = os.path.join(os.environ['USERPROFILE'], '.ssh', 'codesblock_hostinger_20260912')
subprocess.run(['ssh-keygen', '-p', '-P', '""', '-N', '', '-f', key], check=True)
