import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'secure_storage.dart';

/// Riverpod provider that exposes a shared [SecureStorage] instance.
final secureStorageProvider = Provider<SecureStorage>((ref) {
  return SecureStorage();
});
