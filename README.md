# Description

A Docker image that exposes docwire through a simple REST API

# Building

To build:

```bash
docker build --no-cache -t docwire-api .
```

# Running

## Example 1: Basic usage

Run the image and visit http://localhost:9000

```bash
docker run --name=docwire-api -p 9000:80 .
```