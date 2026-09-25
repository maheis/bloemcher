import 'package:flutter/material.dart';

import 'app_controller.dart';
import 'pages/activities_page.dart';
import 'pages/overview_page.dart';
import 'pages/plant_editor_page.dart';
import 'pages/plants_page.dart';

class BloemcherApp extends StatelessWidget {
  const BloemcherApp({super.key, required this.controller});

  final AppController controller;

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: controller,
      builder: (context, _) {
        return MaterialApp(
          debugShowCheckedModeBanner: false,
          title: 'Bloemcher',
          theme: ThemeData(
            useMaterial3: true,
            colorSchemeSeed: const Color(0xFF2E7D32),
            brightness: Brightness.light,
          ),
          darkTheme: ThemeData(
            useMaterial3: true,
            colorSchemeSeed: const Color(0xFFA5D6A7),
            brightness: Brightness.dark,
          ),
          home: BloemcherHomePage(controller: controller),
        );
      },
    );
  }
}

class BloemcherHomePage extends StatefulWidget {
  const BloemcherHomePage({super.key, required this.controller});

  final AppController controller;

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
      appBar: AppBar(title: const Text('Bloemcher'), centerTitle: false),
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
