# Use the official Node.js image
FROM node:18

# Set working directory
WORKDIR /app

# Copy only package files first (for layer caching)
COPY package.json package-lock.json* ./

# Copy the rest of the application source code
COPY . .

# Install dependencies
RUN npm install

# Expose port used by Next.js dev server
EXPOSE 3000

# Start Next.js in development mode
CMD ["npm", "run", "dev"]