# 🚀 Deployment Guide - Taskflow Todo App

This guide walks you through deploying the Todo application to the cloud **without needing PHP installed locally**.

---

## Quick Start (Recommended Path)

| Service | Platform | Free Tier |
|---------|----------|-----------|
| Backend API | Railway | ✅ $5 credit/month |
| Database | Railway MySQL | ✅ Included |
| Frontend | Vercel | ✅ Unlimited |

---

## Step 1: Prepare Your Repository

1. **Create a GitHub repository** and push this code:
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/todo-app.git
   git push -u origin main
   ```

---

## Step 2: Deploy Backend to Railway

### 2.1 Create Railway Account
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub (recommended)

### 2.2 Create New Project
1. Click **"New Project"**
2. Select **"Deploy from GitHub repo"**
3. Choose your `todo-app` repository
4. When asked for root directory, enter: `backend`

### 2.3 Add MySQL Database
1. In your project dashboard, click **"New"**
2. Select **"Database"** → **"MySQL"**
3. Railway will create the database automatically

### 2.4 Configure Environment Variables
1. Click on your backend service
2. Go to **"Variables"** tab
3. Add these variables:

```env
APP_NAME=Taskflow
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_THIS_KEY
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync

FRONTEND_URL=https://your-app.vercel.app
```

### 2.5 Generate APP_KEY
You can use this online tool: https://generate-random.org/laravel-key-generator

Or run this in any PHP environment:
```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```

### 2.6 Deploy
Railway will automatically deploy when you push changes to GitHub.

### 2.7 Get Your Backend URL
After deployment, go to **Settings** → **Domains** and copy your URL (e.g., `https://todo-backend-production.up.railway.app`)

---

## Step 3: Deploy Frontend to Vercel

### 3.1 Create Vercel Account
1. Go to [vercel.com](https://vercel.com)
2. Sign up with GitHub (recommended)

### 3.2 Import Project
1. Click **"Add New"** → **"Project"**
2. Select **"Import Git Repository"**
3. Choose your `todo-app` repository

### 3.3 Configure Project
1. **Root Directory**: `frontend`
2. **Framework Preset**: `Vite` (auto-detected)
3. **Build Command**: `npm run build`
4. **Output Directory**: `dist`

### 3.4 Set Environment Variable
Add this environment variable:
```
VITE_API_URL=https://your-backend.railway.app/api
```
(Replace with your actual Railway backend URL from Step 2.7)

### 3.5 Deploy
Click **"Deploy"** - Vercel will build and deploy your frontend.

---

## Step 4: Update CORS (Important!)

After getting your Vercel URL:

1. Go back to Railway
2. Update the `FRONTEND_URL` variable with your actual Vercel URL:
   ```
   FRONTEND_URL=https://your-app.vercel.app
   ```

---

## 🎉 Done!

Your app should now be live at your Vercel URL!

---

## Alternative: Deploy with Docker Locally

If you want to test locally without installing PHP:

```bash
# Clone the repo
git clone https://github.com/YOUR_USERNAME/todo-app.git
cd todo-app

# Start all services
docker-compose up -d

# Wait for MySQL to be ready, then run migrations
docker-compose exec backend php artisan migrate --seed

# Access the app
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000/api
```

---

## Troubleshooting

### Backend returns 500 error
- Check if `APP_KEY` is set correctly
- Verify database connection variables
- Check Railway logs for detailed errors

### CORS errors in browser
- Ensure `FRONTEND_URL` is set to your exact Vercel domain
- Make sure there's no trailing slash

### Database connection failed
- Railway MySQL might take a minute to start
- Check if the database variables are using Railway's reference syntax: `${{MySQL.VARIABLE}}`

### Frontend can't reach API
- Verify `VITE_API_URL` in Vercel includes `/api` at the end
- Make sure backend is deployed and accessible

---

## Useful Commands

```bash
# Railway CLI (optional)
npm i -g @railway/cli
railway login
railway logs  # View backend logs

# Vercel CLI (optional)
npm i -g vercel
vercel login
vercel logs   # View build logs
```

---

## Cost Estimation

| Service | Free Tier Limits |
|---------|-----------------|
| Railway | $5/month credit, ~500 hours |
| Vercel | Unlimited for hobby |
| **Total** | **$0/month** for small projects |

---

## Need Help?

- Railway Docs: https://docs.railway.app
- Vercel Docs: https://vercel.com/docs
- Laravel Docs: https://laravel.com/docs
- Vue.js Docs: https://vuejs.org/guide
