/// Application-wide configuration values.
///
/// Values can be overridden at build time using `--dart-define`.
class AppConfig {
  const AppConfig._();

  /// Base URL for the channel middleware API.
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://localhost:8000/api/v1',
  );
}
