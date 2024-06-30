import os
import datetime
import pytz
import RPi.GPIO as GPIO

from dotenv import load_dotenv
from time import sleep

load_dotenv("../.env")


# Set the Raspberry Pi GPIO pin number connected to the DO pin of the ldr light sensor module
DO_PIN = 4

# Set the GPIO mode and configure the ldr light sensor module pin as INPUT
GPIO.setmode(GPIO.BCM)
GPIO.setup(DO_PIN, GPIO.IN)

# Initialize the previous state variable with the current state
prev_light_state = GPIO.input(DO_PIN)

try:
    while True:
        # Read the current state of the ldr light sensor module
        light_state = GPIO.input(DO_PIN)

        # Check for a state change (LOW to HIGH or HIGH to LOW)
        if light_state != prev_light_state:

            if light_state == GPIO.LOW:

                # Get the current timestamp
                current_day = datetime.datetime.now(pytz.utc).strftime("%Y-%m-%d")
                current_timestamp = datetime.datetime.now(pytz.utc).strftime("%Y-%m-%dT%H:%M:%SZ")

                # Append the timestamp to the file
                with open("storage/" + current_day + ".log", "a") as file:
                    file.write(current_timestamp + "\n")

                print(f"Timestamp {current_timestamp} appended to {current_day}.log")

            else:
                print("Light is gone!")

        # Update the previous state variable
        prev_light_state = light_state

        # Add a small delay to prevent continuous readings
        sleep(0.1)

except KeyboardInterrupt:
    # Clean up GPIO settings when Ctrl+C is pressed
    GPIO.cleanup()
    print("\nExiting MeterLog")