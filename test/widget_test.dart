import 'package:flutter_test/flutter_test.dart';

import 'package:bloemcher/app.dart';
import 'package:bloemcher/app_controller.dart';
import 'package:bloemcher/models.dart';
import 'package:bloemcher/repository/app_repository.dart';
import 'package:bloemcher/ui_settings.dart';

void main() {
  testWidgets('Bloemcher app loads care overview', (tester) async {
    final controller = AppController(_MemoryAppRepository());
    await controller.load();

    await tester.pumpWidget(
      BloemcherApp(
        controller: controller,
        settingsController: UiSettingsController.memory(),
      ),
    );

    expect(find.text('Bloemcher'), findsOneWidget);
    expect(find.text('Heute dran'), findsOneWidget);
  });

  test('water due respects water period', () async {
    final controller = AppController(_MemoryAppRepository());
    await controller.load();

    final plant = controller.plants.first;

    expect(controller.isWaterDue(plant, now: DateTime(2026, 1, 10)), isTrue);
    expect(controller.isWaterDue(plant, now: DateTime(2026, 1, 3)), isFalse);
  });

  test('watering creates activity and clears due status', () async {
    final controller = AppController(_MemoryAppRepository());
    await controller.load();

    final plant = controller.plants.first;
    await controller.waterPlant(plant, volumeMl: 300);

    expect(controller.activities.first.type, PlantActivityType.water);
    expect(controller.activities.first.comment, '300 ml');
    expect(controller.isWaterDue(plant), isFalse);
  });
}

class _MemoryAppRepository implements AppRepository {
  List<Plant> _plants = const [
    Plant(
      id: 'plant_1',
      room: 'Wohnzimmer',
      name: 'Monstera',
      sortOrder: 10,
      waterPeriodDays: 7,
      waterVolumeMl: 300,
    ),
  ];
  List<PlantActivity> _activities = const [
    PlantActivity(
      id: 'activity_1',
      date: '2026-01-01',
      plantId: 'plant_1',
      type: PlantActivityType.water,
      comment: '300 ml',
    ),
  ];

  @override
  Future<List<Plant>> loadPlants() async => _plants;

  @override
  Future<List<PlantActivity>> loadActivities() async => _activities;

  @override
  Future<void> savePlants(List<Plant> plants) async {
    _plants = plants;
  }

  @override
  Future<void> saveActivities(List<PlantActivity> activities) async {
    _activities = activities;
  }
}
