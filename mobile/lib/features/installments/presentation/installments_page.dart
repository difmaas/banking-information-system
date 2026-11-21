import 'package:flutter/material.dart';

import '../../../shared/widgets/placeholder_page.dart';

/// Installment schedule page.
///
/// Will display upcoming and paid installments for the customer's loans.
class InstallmentsPage extends StatelessWidget {
  const InstallmentsPage({super.key});

  /// Builds the installment schedule placeholder page.
  @override
  Widget build(BuildContext context) {
    return const PlaceholderPage(
      title: 'Angsuran',
      message: 'Jadwal dan riwayat angsuran akan ditampilkan di sini.',
    );
  }
}
