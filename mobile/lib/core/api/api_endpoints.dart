/// Centralized API endpoint paths.
///
/// Keeping endpoint strings in one place avoids typos and makes API changes
/// easier to maintain.
class ApiEndpoints {
  const ApiEndpoints._();

  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String profile = '/me/profile';
  static const String loans = '/me/loans';
  static const String installments = '/me/installments';
  static const String payments = '/me/payments';
}
