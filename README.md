# MeterLog

Python scripts for a RaspberryPi to log electricity usage via a smart meter's Metrology LED. 
This sometimes flashes at 1000 imp/kWh or 3200 imp/kWh notification LED, depending on the meter.


---


## Connecting to Pi

Authentication is via SSH. There is no password access.

```bash
ssh kilncroft@kilncroft
```


---


## Code Deployment


### Set Up Git

```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```


### Clone the Repository

```bash
cd /home/kilncroft/
git clone https://github.com/iamphilrae/MeterLog.git
cd MeterLog
git status # to validate all went well
```


### Deploying Latest Code

Deploying the latest code is as simple as pulling the `main` branch.

```bash
cd /home/kilncroft/MeterLog
git fetch --all
git checkout main
git pull
```


### Installing Dependencies

```bash
# Python
pip install -r requirements.txt

# PHP
composer install
```


---



## Running Logger 

### Ad-hoc

```bash
python app/logger.py
```

### As a Service

```bash
# Copy meterlog.service to systemd
sudo cp /home/kilncroft/MeterLog/app/meterlog.service /etc/systemd/system/

# Reload systemd to recognize the new service and enable it to start at boot
sudo systemctl daemon-reload
sudo systemctl enable meterlog.service

# Start the service
sudo systemctl start meterlog.service

# Check the status
sudo systemctl status meterlog.service

# Ensure script is executable
chmod +x /home/kilncroft/MeterLog/app/logger.py

# Check logs of the service
journalctl -u meterlog.service -f
```


---



## Uploading Logs to S3

### Ad-hoc

```bash
php app/s3_upload.php
```

### As a Scheduled Task

```bash
# Ensure script is executable
chmod +x /home/kilncroft/MeterLog/app/s3_upload.php

# Open the cron editor
crontab -e

# Add the following line - runs once every hour
0 * * * * /usr/bin/php /home/kilncroft/MeterLog/app/s3_upload.php
```