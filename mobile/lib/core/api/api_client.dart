import 'package:dio/dio.dart';

import '../config/app_config.dart';

/// HTTP client wrapper around Dio.
///
/// Centralizes base URL, timeouts, and future interceptors for all API calls.
class ApiClient {
  /// Creates an API client, optionally accepting a custom Dio instance for testing.
  ApiClient({Dio? dio})
    : _dio =
          dio ??
          Dio(
            BaseOptions(
              baseUrl: AppConfig.apiBaseUrl,
              connectTimeout: const Duration(seconds: 15),
              receiveTimeout: const Duration(seconds: 15),
            ),
          );

  /// Underlying Dio instance used by repositories and services.
  Dio get dio => _dio;

  final Dio _dio;
}
