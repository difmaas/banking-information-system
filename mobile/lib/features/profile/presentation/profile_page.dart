import 'package:flutter/material.dart';

import '../../../shared/widgets/placeholder_page.dart';

/// Customer profile page.
///
/// Will display identity, employment, income, and document information from CIS.
class ProfilePage extends StatelessWidget {
  const ProfilePage({super.key});

  /// Builds the profile placeholder page.
  @override
  Widget build(BuildContext context) {
    return const PlaceholderPage(
      title: 'Profil',
      message:
          'Profil nasabah, identitas, pekerjaan, dan dokumen akan ditampilkan di sini.',
    );
  }
}
