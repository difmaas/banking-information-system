import 'package:bis_mobile/app/app.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  // Verifies that the app starts on the login page and renders the main action.
  testWidgets('shows login page', (WidgetTester tester) async {
    await tester.pumpWidget(const ProviderScope(child: BisApp()));
    await tester.pumpAndSettle();

    expect(find.text('BIS Mobile'), findsOneWidget);
    expect(find.widgetWithText(FilledButton, 'Masuk'), findsOneWidget);
  });
}
