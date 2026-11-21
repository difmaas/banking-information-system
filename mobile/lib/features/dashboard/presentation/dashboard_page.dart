import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

/// Customer dashboard with shortcuts to the main BIS features.
class DashboardPage extends StatelessWidget {
  const DashboardPage({super.key});

  /// Builds the dashboard menu and navigation actions.
  @override
  Widget build(BuildContext context) {
    const items = <_DashboardItem>[
      _DashboardItem(
        title: 'Profil',
        subtitle: 'Identitas dan data nasabah',
        route: '/profile',
        icon: Icons.person_outline,
      ),
      _DashboardItem(
        title: 'Pinjaman',
        subtitle: 'Pengajuan dan status pinjaman',
        route: '/loans',
        icon: Icons.account_balance_outlined,
      ),
      _DashboardItem(
        title: 'Angsuran',
        subtitle: 'Jadwal dan riwayat angsuran',
        route: '/installments',
        icon: Icons.event_note_outlined,
      ),
      _DashboardItem(
        title: 'Pembayaran',
        subtitle: 'Tagihan dan bukti pembayaran',
        route: '/payments',
        icon: Icons.receipt_long_outlined,
      ),
    ];

    return Scaffold(
      appBar: AppBar(title: const Text('Dashboard')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          for (final item in items)
            Card(
              child: ListTile(
                leading: Icon(item.icon),
                title: Text(item.title),
                subtitle: Text(item.subtitle),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => context.go(item.route),
              ),
            ),
        ],
      ),
    );
  }
}

/// Menu item configuration for the dashboard.
class _DashboardItem {
  const _DashboardItem({
    required this.title,
    required this.subtitle,
    required this.route,
    required this.icon,
  });

  final String title;
  final String subtitle;
  final String route;
  final IconData icon;
}
