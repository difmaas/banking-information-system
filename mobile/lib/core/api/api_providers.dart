import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'api_client.dart';

/// Riverpod provider that exposes a shared [ApiClient] instance.
final apiClientProvider = Provider<ApiClient>((ref) {
  return ApiClient();
});
