@echo off
git init
git add .
git commit -m "Initial commit - Backend"
git remote add origin "https://github.com/cuongdesignnb/winebe.git"
git branch -M main
git push -u origin main
