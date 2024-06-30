# MeterLog

Python scripts for a RaspberryPi to log electricity usage via a smart meter's "1000 imp/kWh" notification light.

## Connecting to Pi

Authentication is via SSH. There is no password access.

```bash
ssh kilncroft@kilncroft
```

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


### Installing Python Dependencies

```bash
# Install dependencies from Pipfile
pipenv install

# Start a virtual environment
pipenv shell
```


### Running Application

```bash
python app/...
```