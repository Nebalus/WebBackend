# Hurl API Tests

This directory contains Hurl template files for testing the Nebalus WebBackend API endpoints.

> [!NOTE]
> **Template Files**: This directory contains example/template `.hurl` files that demonstrate common API testing patterns. Use these as references to create additional hurl files for your other endpoints. The Bruno collection in the `endpoints/` folder contains the complete set of 64 API definitions.

## What is Hurl?

[Hurl](https://hurl.dev/) is a command-line tool that runs HTTP requests defined in a simple plain text format. It's perfect for:
- Testing REST APIs
- Running integration tests in CI/CD
- Documenting API endpoints as executable tests
- Quick manual API verification

## Installation

### Linux
```bash
# Download latest release
curl -LO https://github.com/Orange-OpenSource/hurl/releases/download/4.3.0/hurl_4.3.0_amd64.deb
sudo dpkg -i hurl_4.3.0_amd64.deb
```

### macOS
```bash
brew install hurl
```

### Other platforms
See [official installation guide](https://hurl.dev/docs/installation.html)

## Setup

1. **Copy environment template**:
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env` with your credentials**:
   ```bash
   BASE_URL=http://localhost:80
   USERNAME=your_username
   PASSWORD=your_password
   ```

3. **Make sure your backend server is running**:
   ```bash
   # Navigate to project root and start the server
   cd /home/nebalus/projects/NebalusWeb/WebBackend
   # Use your usual start command (docker-compose, php server, etc.)
   ```

## Usage

### Running Individual Tests

```bash
# Source environment variables
source .env

# Test health endpoint (no auth required)
hurl --variable BASE_URL=$BASE_URL health.hurl

# Test info endpoint
hurl --variable BASE_URL=$BASE_URL info.hurl

# Test metrics endpoint
hurl --variable BASE_URL=$BASE_URL metrics.hurl
```

### Authentication Flow

First, authenticate to get a JWT token:

```bash
source .env
hurl --variable BASE_URL=$BASE_URL \
     --variable USERNAME=$USERNAME \
     --variable PASSWORD=$PASSWORD \
     ui/user/auth.hurl
```

The auth endpoint will return a JWT token. To use it in subsequent requests, capture it:

```bash
# Run auth and capture JWT to a file
AUTH_JWT=$(hurl --variable BASE_URL=$BASE_URL \
                --variable USERNAME=$USERNAME \
                --variable PASSWORD=$PASSWORD \
                ui/user/auth.hurl 2>&1 | grep -oP '(?<=AUTH_JWT: ).*')

# Now use the token in authenticated endpoints
hurl --variable BASE_URL=$BASE_URL \
     --variable AUTH_JWT=$AUTH_JWT \
     ui/user/get_user_permissions.hurl
```

### Running Multiple Tests

You can chain multiple hurl files together:

```bash
source .env

# Run all health checks
hurl --variable BASE_URL=$BASE_URL \
     health.hurl \
     info.hurl \
     metrics.hurl

# Run all blog endpoints
hurl --variable BASE_URL=$BASE_URL \
     --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/*.hurl
```

### Verbose Output

Add `--verbose` or `--very-verbose` to see detailed request/response information:

```bash
hurl --verbose --variable BASE_URL=$BASE_URL health.hurl
```

### Testing with Different Environments

```bash
# Development
BASE_URL=http://localhost:80 hurl --variable BASE_URL=$BASE_URL health.hurl

# Production
BASE_URL=https://api.nebalus.dev hurl --variable BASE_URL=$BASE_URL health.hurl
```

## Directory Structure

```
hurl/
├── README.md                         # This file
├── .env.example                      # Environment variable template
├── health.hurl                       # Health check endpoint (no auth)
├── info.hurl                         # Info endpoint (no auth)
├── metrics.hurl                      # Metrics endpoint (no auth)
├── services/                         # Service endpoints examples
│   ├── linktree_click.hurl          # GET with URL parameters
│   └── referral_click.hurl          # GET with URL parameters
└── ui/
    ├── admin/                        # Admin endpoint examples
    │   ├── roles/
    │   │   └── getall_roles.hurl    # GET with auth + admin permissions
    │   └── users/
    │       └── roles/
    │           └── add_user_to_role.hurl  # POST with path params
    ├── user/                         # User endpoint examples
    │   ├── auth.hurl                # POST with JSON + JWT capture
    │   └── get_user_permissions.hurl # GET with auth + path params
    └── feature/                      # Feature module examples
        └── blog/
            ├── create_blog.hurl     # POST with JSON body
            └── getall_blogs.hurl    # GET with auth (list)
```

## Template Patterns

The example files demonstrate these common patterns:

| Pattern | Example File | Shows |
|---------|-------------|-------|
| **No Auth GET** | `health.hurl` | Simple GET request with assertions |
| **POST + JSON + Capture** | `ui/user/auth.hurl` | Authentication with JWT token capture |
| **GET + Auth + Path Params** | `ui/user/get_user_permissions.hurl` | Authenticated GET with URL parameters |
| **POST + Auth + JSON** | `ui/feature/blog/create_blog.hurl` | Creating resources with auth |
| **POST + Path Params** | `ui/admin/users/roles/add_user_to_role.hurl` | Multiple path parameters |
| **GET + URL Variable** | `services/linktree_click.hurl` | Dynamic URL construction |
| **Admin Endpoints** | `ui/admin/roles/getall_roles.hurl` | Admin-level permissions |

## Common Workflows

### Complete Blog CRUD Test

```bash
source .env

# 1. Authenticate
AUTH_JWT=$(hurl --variable BASE_URL=$BASE_URL \
                --variable USERNAME=$USERNAME \
                --variable PASSWORD=$PASSWORD \
                ui/user/auth.hurl 2>&1 | grep -oP '(?<=AUTH_JWT: ).*')

# 2. Create a blog
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/create_blog.hurl

# 3. Get all blogs
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/getall_blogs.hurl

# 4. Get specific blog
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/get_blog.hurl

# 5. Edit blog
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/edit_blog.hurl

# 6. Delete blog
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/feature/blog/delete_blog.hurl
```

### Admin Role Management

```bash
source .env
AUTH_JWT="your_admin_jwt_token"

# Get all roles
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/admin/roles/getall_roles.hurl

# Create new role
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/admin/roles/create_role.hurl

# Assign permissions to role
hurl --variable BASE_URL=$BASE_URL --variable AUTH_JWT=$AUTH_JWT \
     ui/admin/roles/permissions/upsert_role_permissions.hurl
```

## Tips

1. **Use `--test` flag** for CI/CD pipelines - it returns non-zero exit code on failure
2. **Add assertions** in your .hurl files to validate responses
3. **Use captures** to chain requests and pass data between them
4. **Store sensitive data** in `.env` file (already gitignored)
5. **Run with `--json`** to get machine-readable output

## Troubleshooting

**Connection refused?**
- Make sure your backend server is running
- Check that BASE_URL matches your server address

**401 Unauthorized?**
- Verify your username and password in `.env`
- Check that your JWT token hasn't expired
- Re-authenticate to get a fresh token

**404 Not Found?**
- Verify the endpoint URL is correct
- Check your backend routes configuration

## Further Reading

- [Hurl Documentation](https://hurl.dev/docs/manual.html)
- [Hurl Tutorial](https://hurl.dev/docs/tutorial/your-first-hurl-file.html)
- [Hurl Samples](https://hurl.dev/docs/samples.html)
