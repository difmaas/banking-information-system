import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Secure storage wrapper for sensitive values such as authentication tokens.
class SecureStorage {
  SecureStorage({FlutterSecureStorage? storage})
    : _storage = storage ?? const FlutterSecureStorage();

  static const String _tokenKey = 'auth_token';

  /// Saves the authentication token.
  Future<void> saveToken(String token) {
    return _storage.write(key: _tokenKey, value: token);
  }

  /// Reads the stored authentication token, if any.
  Future<String?> readToken() {
    return _storage.read(key: _tokenKey);
  }

  /// Removes all stored values, including the authentication token.
  Future<void> clear() {
    return _storage.deleteAll();
  }

  final FlutterSecureStorage _storage;
}
