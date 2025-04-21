# PhastAuth - RESTful API with PHP and JWT Authentication

## Requirements

- PHP 8.2+
- MySQL 8.0+

## API Endpoints

### User Routes
- `POST /users/create` - Create new user
- `POST /users/login` - Authenticate user (returns JWT)
- `POST /users/refresh` - Refresh JWT token
- `POST /users/logout` - Invalidate current token
- `GET /users/fetch` - Retrieve authenticated user's data
- `PUT /users/update` - Update user information
- `DELETE /users/delete` - Soft delete user account

## Security

- JWT (JSON Web Tokens) authentication
- Password hashing with argon2
- Token refresh mechanism
- Secure HTTP headers
- Input validation and sanitization

## Technical Implementation

- Built with pure PHP (no frameworks)
- MySQL stored procedures for data operations
- RESTful architecture
- Proper HTTP status codes
- Request testing with Insomnia

## Documentations

- [Database Documentation](docs/DATABASE.md)
- [API Documentation](docs/API.md)

## License

[MIT License](LICENSE)