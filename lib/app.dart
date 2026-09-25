import 'package:flutter/material.dart';

import 'app_controller.dart';
import 'pages/activities_page.dart';
import 'pages/overview_page.dart';
import 'pages/plant_editor_page.dart';
import 'pages/plants_page.dart';
import 'ui_settings.dart';

class BloemcherApp extends StatelessWidget {
  const BloemcherApp({
    super.key,
    required this.controller,
    required this.settingsController,
  });

  final AppController controller;
  final UiSettingsController settingsController;

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: Listenable.merge([controller, settingsController]),
      builder: (context, _) {
        final settings = settingsController.settings;
        return MaterialApp(
          debugShowCheckedModeBanner: false,
          title: 'Bloemcher',
          theme: buildUnifiedTheme(settings, Brightness.light),
          darkTheme: buildUnifiedTheme(settings, Brightness.dark),
          themeMode: settings.useLightTheme ? ThemeMode.light : ThemeMode.dark,
          builder: (context, child) => MediaQuery(
            data: MediaQuery.of(
              context,
            ).copyWith(textScaler: TextScaler.linear(settings.textScaleFactor)),
            child: child ?? const SizedBox.shrink(),
          ),
          home: BloemcherHomePage(
            controller: controller,
            settingsController: settingsController,
          ),
        );
      },
    );
  }
}

class BloemcherHomePage extends StatefulWidget {
  const BloemcherHomePage({
    super.key,
    required this.controller,
    required this.settingsController,
  });

  final AppController controller;
  final UiSettingsController settingsController;

  @override
  State<BloemcherHomePage> createState() => _BloemcherHomePageState();
}

class _BloemcherHomePageState extends State<BloemcherHomePage> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final pages = <Widget>[
      OverviewPage(controller: widget.controller),
      PlantsPage(controller: widget.controller),
      ActivitiesPage(controller: widget.controller),
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Bloemcher'),
        centerTitle: false,
        actions: [
          IconButton(
            tooltip: 'Einstellungen',
            onPressed: () async {
              final result = await Navigator.of(context).push<AppUiSettings>(
                MaterialPageRoute(
                  builder: (_) => UiSettingsPage(
                    initial: widget.settingsController.settings,
                  ),
                ),
              );
              if (result != null) {
                await widget.settingsController.update(result);
              }
            },
            icon: const Icon(Icons.settings_outlined),
          ),
        ],
      ),
      body: pages[_index],
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (_) => PlantEditorPage(controller: widget.controller),
            ),
          );
        },
        icon: const Icon(Icons.add),
        label: const Text('Pflanze'),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (value) => setState(() => _index = value),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.water_drop_outlined),
            selectedIcon: Icon(Icons.water_drop),
            label: 'Pflege',
          ),
          NavigationDestination(
            icon: Icon(Icons.local_florist_outlined),
            selectedIcon: Icon(Icons.local_florist),
            label: 'Pflanzen',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_outlined),
            selectedIcon: Icon(Icons.history),
            label: 'Aktivität',
          ),
        ],
      ),
    );
  }
}
