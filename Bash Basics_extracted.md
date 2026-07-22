BOSS’s Bash Mastery Roadmap
Step 1 — Bash & Terminal Basics
Goal: Understand what Bash is and the terminal interface.Commands to practice:
pwd
whoami
echo "Hello Bash"
Drill:
pwd → check your location.
whoami → know your user.
Echo → write the content.
Step 2 — Navigating the Filesystem
Goal: Move around folders efficiently.Commands:
cd /           # root
cd ~          # home
cd ..          # go back
ls               # list files
ls -a          # show hidden
Step 3 — Creating & Managing Files/Folders
Goal: Make folders and files, edit with nano.Commands:
mkdir practice ----------------Creates a new directory
cd practice---------------------Creates a new empty folder 
touch demo.txt----------------Creates a file
nano demo.txt-----------------it edits the file
ls -l------------------------------show the list of the file 
cp demo.txt demo_copy.txt---Copy the file
mv demo.txt demo_orig.txt----move/rename the file
rm demo_copy.txt---------------remove/ delete the file
rmdir empty_folder-------------remove the empty folder
rm -r folder_with_content-----remove the folder 
Step 4 — Viewing File Content
Goal: Read and explore files.Commands:
cat file.txt--------------------Print the content which is in the file
head -n 10 file.txt
tail -n 10 file.txt
less file.txt
Step 5 — Redirection & Pipelines
Goal: Control input/output, chain commands.Commands:
echo "Hello" > out.txt----------print the content in the file
echo "More" >> out.txt
cat out.txt | grep Hello---------show the content 
cat out.txt | tee output.txt
Step 6 — Permissions & Ownership
Goal: Understand file access and users.Commands:
ls -l
chmod u+x script.sh
chmod 640 file.txt
chown user:group file.txt
umask
Step 7 — Processes & Job Control
Goal: Manage running programs.Commands:
ps aux
top
jobs
long_task &
fg %1
bg %1
kill -TERM <pid>
Step 8 — Package Management
Goal: Install and manage programs (Ubuntu/Debian).Commands:
sudo apt update
sudo apt install git curl nano -y
apt list --installed
sudo apt remove nano -y
Step 9 — Variables & Substitution
Goal: Store and use data in scripts.Commands:
name="BOSS"
echo $name
today=$(date +%F)
echo $today
x=5
y=3
echo $((x+y))
Drill:
Make 3 variables: name, age, city → print combined sentence.
Step 10 — Conditionals
Goal: Make decisions in scripts.Commands:
if [[ -f file.txt ]]; then echo "exists"; fi
x=7   if [[ $x -gt 10 ]]; then echo "big"; else echo "small"; fi
case "$choice" in start) echo start;; stop) echo stop;; *) echo unknown;; esac
Step 11 — Loops
Goal: Repeat tasks automatically.Commands:
for i in {1..5}; do echo $i; done
while read line; do echo $line; done < file.txt
Step 12 — Functions
Goal: Reuse code in scripts.Commands:
greet(){ echo "Hello $1"; }
greet BOSS
Step 13 — Reading Input
Goal: Interact with user.Commands:
read -p "Name: " name
echo "Hello $name"
read -rs -p "Password: " pass; echo
Step 14 — Advanced Expansion & Shell Options
Goal: Learn powerful Bash features.Commands:
set -Eeuo pipefail
${var:-default}
${var%.txt}
${var^^}
Step 15 — Text Processing
Goal: Analyze text/files quickly.Commands:
grep "error" file.log
awk '{print $1}' data.csv
sed 's/foo/bar/g' file.txt
sort file.txt | uniq -c
Drill:
Find top 3 repeated words in a text file.
Step 16 — Archives & Compression
tar -czf backup.tgz folder/
tar -xzf backup.tgz
zip -r file.zip folder/
unzip file.zip
Drill:
Compress your practice folder, extract it, verify files.
Step 17 — Scheduling & Automation
crontab -e
# 30 2 * * * /home/user/backup.sh
Drill:
Write script to print date → run manually first.
Step 18 — Shell Customization
alias ll='ls -alF'
export PATH="$HOME/bin:$PATH"
PS1='\u@\h:\w\$ '
source ~/.bashrc
Drill:
Make an alias gs → git status.
Step 19 — Networking Essentials
ip addr show
ping -c3 8.8.8.8
curl -I https://example.com
ss -tulpn
Step 20 — Debugging & Safety
set -x
trap 'echo Error at $LINENO' ERR
shellcheck script.sh