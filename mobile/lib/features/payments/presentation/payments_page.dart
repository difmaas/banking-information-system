import 'package:flutter/material.dart';

import '../../../shared/widgets/placeholder_page.dart';

/// Payment page.
///
/// Will display outstanding bills and payment receipts for the customer.
class PaymentsPage extends StatelessWidget {
  const PaymentsPage({super.key});

  /// Builds the payment placeholder page.
  @override
  Widget build(BuildContext context) {
    return const PlaceholderPage(
      title: 'Pembayaran',
      message: 'Tagihan dan bukti pembayaran akan ditampilkan di sini.',
    );
  }
}
