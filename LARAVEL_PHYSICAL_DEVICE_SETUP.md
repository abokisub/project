# Laravel Server Setup for Physical Device Connection

## 🎯 Quick Start

Your current IP address: **10.39.185.234** ✅ (Already configured in Flutter app)

## 📱 Step-by-Step Setup

### Step 1: Start Laravel Server for Network Access

**IMPORTANT:** Use this command to allow connections from your physical device:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Why `--host=0.0.0.0`?**
- `localhost` or `127.0.0.1` only allows connections from the same computer
- `0.0.0.0` allows connections from any device on your network (including your phone)

### Step 2: Verify Server is Running

You should see:
```
INFO  Server running on [http://0.0.0.0:8000]
```

### Step 3: Test from Your Phone

1. Make sure your phone is on the **same WiFi network** as your computer
2. Open browser on your phone and go to:
   ```
   http://10.39.185.234:8000
   ```
3. You should see your Laravel app

### Step 4: Test API Endpoint

On your phone browser, try:
```
http://10.39.185.234:8000/api/v1/auth/login
```

---

## 🔧 Flutter App Configuration

Your Flutter app is already configured correctly:
- File: `Kobopoint Mobile/lib/core/config/app_config.dart`
- Current setting: `http://10.39.185.234:8000/api/v1` ✅

**No changes needed!** Just make sure:
- `useProduction = false` (for development)
- `_devBaseUrlPhysical` has your IP (already set correctly)

---

## 🚨 Troubleshooting

### Problem: "Connection refused" or "Unable to connect"

**Solution 1: Check Windows Firewall**
1. Open Windows Defender Firewall
2. Click "Allow an app or feature through Windows Firewall"
3. Make sure PHP is allowed (or temporarily disable firewall for testing)

**Solution 2: Check Network Connection**
- Make sure phone and computer are on the **same WiFi network**
- Try pinging from phone: Open terminal app and type:
  ```
  ping 10.39.185.234
  ```

**Solution 3: Verify Server is Running**
- Check that Laravel server shows: `Server running on [http://0.0.0.0:8000]`
- Try accessing `http://10.39.185.234:8000` from your computer's browser

**Solution 4: Check IP Address**
- If your IP changes, run this command to find new IP:
  ```powershell
  ipconfig | Select-String -Pattern "IPv4"
  ```
- Update `_devBaseUrlPhysical` in `app_config.dart` with new IP

---

## 📝 Complete Command Reference

### Start Server (Network Access)
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Start Server (Localhost Only - NOT for physical device)
```bash
php artisan serve
# or
php artisan serve --host=127.0.0.1 --port=8000
```

### Find Your IP Address (Windows)
```powershell
ipconfig | Select-String -Pattern "IPv4"
```

### Find Your IP Address (Mac/Linux)
```bash
ifconfig | grep "inet "
# or
ip addr | grep "inet "
```

---

## ✅ Verification Checklist

- [ ] Laravel server running with `--host=0.0.0.0`
- [ ] Phone and computer on same WiFi network
- [ ] Can access `http://10.39.185.234:8000` from phone browser
- [ ] Flutter app has correct IP in `app_config.dart`
- [ ] Windows Firewall allows PHP/Laravel
- [ ] API endpoint accessible: `http://10.39.185.234:8000/api/v1`

---

## 🎉 You're Ready!

Once the server is running with `--host=0.0.0.0`, your Flutter app on your physical device will be able to connect to your Laravel backend!

