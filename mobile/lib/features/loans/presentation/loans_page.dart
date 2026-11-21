import 'package:flutter/material.dart';

import '../../../shared/widgets/placeholder_page.dart';

/// Loan list page.
///
/// Will display loan applications and their current statuses from LNS.
class LoansPage extends StatelessWidget {
  const LoansPage({super.key});

  /// Builds the loan list placeholder page.
  @override
  Widget build(BuildContext context) {
    return const PlaceholderPage(
      title: 'Pinjaman',
      message: 'Daftar pengajuan dan status pinjaman akan ditampilkan di sini.',
    );
  }
}
