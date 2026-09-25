import 'package:sembast/sembast.dart';

import '../models.dart';

abstract interface class AppRepository {
  Future<List<Plant>> loadPlants();
  Future<List<PlantActivity>> loadActivities();
  Future<void> savePlants(List<Plant> plants);
  Future<void> saveActivities(List<PlantActivity> activities);
}

class LocalAppRepository implements AppRepository {
  LocalAppRepository(this._database);

  final Database _database;
  final _plantsStore = stringMapStoreFactory.store('plants');
  final _activitiesStore = stringMapStoreFactory.store('plant_activities');

  @override
  Future<List<Plant>> loadPlants() async {
    final plants = (await _plantsStore.find(_database))
        .map((entry) => Plant.fromJson(entry.value))
        .toList();

    if (plants.isEmpty) {
      final seed = _defaultPlants();
      await savePlants(seed);
      return seed;
    }

    return plants;
  }

  @override
  Future<List<PlantActivity>> loadActivities() async {
    final activities = (await _activitiesStore.find(_database))
        .map((entry) => PlantActivity.fromJson(entry.value))
        .toList();

    if (activities.isEmpty) {
      final seed = _defaultActivities();
      await saveActivities(seed);
      return seed;
    }

    return activities;
  }

  @override
  Future<void> savePlants(List<Plant> plants) async {
    await _database.transaction((txn) async {
      await _plantsStore.delete(txn);
      for (final plant in plants) {
        await _plantsStore.record(plant.id).put(txn, plant.toJson());
      }
    });
  }

  @override
  Future<void> saveActivities(List<PlantActivity> activities) async {
    await _database.transaction((txn) async {
      await _activitiesStore.delete(txn);
      for (final activity in activities) {
        await _activitiesStore.record(activity.id).put(txn, activity.toJson());
      }
    });
  }

  List<Plant> _defaultPlants() => const [
    Plant(
      id: 'plant_1',
      room: 'Wohnzimmer',
      name: 'Monstera',
      sortOrder: 10,
      latinName: 'Monstera deliciosa',
      waterPeriodDays: 7,
      waterVolumeMl: 350,
      comment: 'Mag helles indirektes Licht.',
    ),
    Plant(
      id: 'plant_2',
      room: 'Küche',
      name: 'Basilikum',
      sortOrder: 20,
      latinName: 'Ocimum basilicum',
      waterPeriodDays: 2,
      waterVolumeMl: 180,
      comment: 'Nicht austrocknen lassen.',
    ),
    Plant(
      id: 'plant_3',
      room: 'Arbeitszimmer',
      name: 'Bogenhanf',
      sortOrder: 30,
      latinName: 'Sansevieria trifasciata',
      waterPeriodDays: 21,
      waterVolumeMl: 120,
      comment: 'Sehr sparsam gießen.',
    ),
  ];

  List<PlantActivity> _defaultActivities() {
    final now = DateTime.now();
    final older = now.subtract(const Duration(days: 9));
    final recent = now.subtract(const Duration(days: 1));

    return [
      PlantActivity(
        id: 'activity_1',
        date: _formatDate(older),
        plantId: 'plant_1',
        type: PlantActivityType.water,
        comment: '350 ml',
      ),
      PlantActivity(
        id: 'activity_2',
        date: _formatDate(recent),
        plantId: 'plant_2',
        type: PlantActivityType.water,
        comment: '180 ml',
      ),
      PlantActivity(
        id: 'activity_3',
        date: _formatDate(now.subtract(const Duration(days: 400))),
        plantId: 'plant_3',
        type: PlantActivityType.repot,
        comment: 'Frische Erde',
      ),
    ];
  }

  String _formatDate(DateTime date) {
    final year = date.year.toString().padLeft(4, '0');
    final month = date.month.toString().padLeft(2, '0');
    final day = date.day.toString().padLeft(2, '0');
    return '$year-$month-$day';
  }
}
