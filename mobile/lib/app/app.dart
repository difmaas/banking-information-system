import 'package:flutter/material.dart';

import 'router.dart';
import 'theme.dart';

/// Root widget of the BIS mobile application.
class BisApp extends StatelessWidget {
  const BisApp({super.key});

  /// Builds the MaterialApp with the application theme and GoRouter configuration.
  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'BIS Mobile',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      routerConfig: router,
    );
  }
}
