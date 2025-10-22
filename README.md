
**Goal:** Establish the working environment and understand the structural differences in the new, minimalist Laravel 11 framework.

### A. Confirming Prerequisites (PHP & Extensions)

| Requirement  | Command to Check (Example) | Status |
| ------------ | -------------------------- | ------ |
| **PHP 8.2+** | `php -v`                   |        |

### B. Installing Composer & The Laravel Installer

**Composer** is the official PHP dependency manager. We also use the convenience wrapper, **The Laravel Installer**.

```bash
# 1. Check Composer access
composer -V
 # 2. Install the Laravel installer globally
composer global require laravel/installer
```

### A. Creating an Application

The recommended command uses the global Laravel installer, providing a guided, interactive experience:

```bash
laravel new example-app
```

### B. Essential Security: Generating the Application Key

This step is **critical** for all secure processes, including encryption, cookie signing, and session handling.

```bash
cd example-app
php artisan key:generate
```

### C. Create the Data Base

Before starting the app we need to create our database, here will use MySql as an example.

```shell
mysql -u root -p
```

```sql
Create database {name of your db};
```

> [!note]
> > If your password doesn't work you should check if MySQL80 is running .
> >
### Starting the Development Server

We use the combined `dev` Composer script to handle multiple services at once.

```bash
# 1. Install frontend dependencies (if using a starter kit):
npm install && npm run build
# 2. Run the Combined Development Script:
composer run dev
# Your application will be accessible at http://localhost:8000

```

---
---

### D. Creating a GitHub Repository & Committing Your Project

Version control ensures your progress is tracked and shared safely.
Follow these steps to create a new Git repository and connect it to GitHub.

```shell
# 1. Initialize a local Git repository
git init

# 2. Check current Git status
git status

# 3. Add all project files to Git tracking
git add .

# 4. Commit the initial version of your project
git commit -m "Initial commit"

#5. Create the main branch
git branch -M main

```

#### Connecting the Local Repo to GitHub Manually

If you created the repo on GitHub via the website:

```shell
# 1. Add the remote GitHub repository URL (replace with your repo)
git remote add origin https://github.com/<your-username>/<your-repo>.git

# 2. Verify the remote URL
git remote -v

# 3. Push your local commits to GitHub
git push -u origin main

```

> [!tip]
> You can use `main` or `master` as the branch name depending on your Git configuration.
> Always confirm with `git branch` before pushing.

---

### E. Working with Branches (optional)

Branches help organize development work by isolating new features, bug fixes, or experiments.
They prevent changes from affecting the main codebase until approved and merged.

```shell
# 1. Check your current branch
git branch

# 2. Create a new branch for a specific feature
git branch auth-system

# 3. Switch to the new branch
git checkout auth-system

# or combine both steps:
git checkout -b auth-system

# 4. Make your changes, then add and commit
git add .
git commit -m "Add authentication system scaffolding"

# 5. Push the new branch to GitHub
git push -u origin auth-system

# 6. After review, merge your branch into main
git checkout main
git merge auth-system

git push

```
