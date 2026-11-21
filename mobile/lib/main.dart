import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'app/app.dart';

/// Application entry point.
///
/// Wraps the app in a Riverpod [ProviderScope] so providers can be accessed
/// throughout the widget tree.
void main() {
  runApp(const ProviderScope(child: BisApp()));
}
