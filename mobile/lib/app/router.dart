import 'package:go_router/go_router.dart';

import '../features/auth/presentation/login_page.dart';
import '../features/auth/presentation/register_page.dart';
import '../features/dashboard/presentation/dashboard_page.dart';
import '../features/installments/presentation/installments_page.dart';
import '../features/loans/presentation/loan_detail_page.dart';
import '../features/loans/presentation/loans_page.dart';
import '../features/payments/presentation/payments_page.dart';
import '../features/profile/presentation/profile_page.dart';

/// Application router.
///
/// Defines the public and customer-facing routes for the BIS mobile client.
/// The initial route is the login page.
final GoRouter router = GoRouter(
  initialLocation: '/login',
  routes: [
    GoRoute(path: '/login', builder: (context, state) => const LoginPage()),
    GoRoute(
      path: '/register',
      builder: (context, state) => const RegisterPage(),
    ),
    GoRoute(
      path: '/dashboard',
      builder: (context, state) => const DashboardPage(),
    ),
    GoRoute(path: '/profile', builder: (context, state) => const ProfilePage()),
    GoRoute(path: '/loans', builder: (context, state) => const LoansPage()),
    GoRoute(
      path: '/loans/:id',
      builder: (context, state) =>
          LoanDetailPage(loanId: state.pathParameters['id']!),
    ),
    GoRoute(
      path: '/installments',
      builder: (context, state) => const InstallmentsPage(),
    ),
    GoRoute(
      path: '/payments',
      builder: (context, state) => const PaymentsPage(),
    ),
  ],
);
