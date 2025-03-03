# Use the official Python 3.11.9 image
FROM python:3.11.9

# Set the working directory inside the container
WORKDIR /app

# Copy the current directory contents into the container
COPY . .

# Install dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Expose port 5000
EXPOSE 5000

# Ensure only 1 instance of Uvicorn runs
CMD ["uvicorn", "server:app", "--host", "0.0.0.0", "--port", "5000", "--workers", "1"]
