import 'package:flutter/material.dart';

import '../../../shared/widgets/placeholder_page.dart';

/// Loan detail page.
///
/// Will display loan information, installment schedule, and payment history.
class LoanDetailPage extends StatelessWidget {
  const LoanDetailPage({super.key, required this.loanId});

  final String loanId;

  /// Builds the loan detail placeholder page.
  @override
  Widget build(BuildContext context) {
    return PlaceholderPage(
      title: 'Pinjaman $loanId',
      message:
          'Detail pinjaman, jadwal angsuran, dan riwayat pembayaran akan ditampilkan di sini.',
    );
  }
}
