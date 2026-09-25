import 'package:flutter/foundation.dart';

import 'models.dart';
import 'repository/app_repository.dart';

class AppController extends ChangeNotifier {
  AppController(this._repository);

  final AppRepository _repository;

  List<Plant> plants = const [];
  List<PlantActivity> activities = const [];
  bool isLoaded = false;

  Future<void> load() async {
    plants = [...await _repository.loadPlants()];
    activities = [...await _repository.loadActivities()];
    _sortPlants();
    _sortActivities();
    isLoaded = true;
    notifyListeners();
  }

  List<Plant> get activePlants =>
      plants.where((plant) => !plant.isArchived).toList();

  List<String> get rooms {
    final rooms = activePlants.map((plant) => plant.room).toSet().toList()
      ..sort();
    return rooms;
  }

  List<Plant> plantsForRoom(String? room) {
    final filtered = room == null || room == 'Alle'
        ? activePlants
        : activePlants.where((plant) => plant.room == room).toList();
    return [...filtered]..sort((a, b) {
      final roomCompare = a.room.compareTo(b.room);
      if (roomCompare != 0) return roomCompare;
      return a.sortOrder.compareTo(b.sortOrder);
    });
  }

  List<Plant> dueWaterPlants({DateTime? now}) {
    return activePlants.where((plant) => isWaterDue(plant, now: now)).toList()
      ..sort((a, b) => a.room.compareTo(b.room));
  }

  int dueWaterVolumeMl({DateTime? now}) {
    return dueWaterPlants(now: now)
        .fold<int>(0, (sum, plant) => sum + plant.waterVolumeMl);
  }

  List<PlantActivity> activitiesForPlant(String plantId) {
    return activities.where((activity) => activity.plantId == plantId).toList();
  }

  Plant? plantById(String id) =>
      plants.firstWhereOrNull((plant) => plant.id == id);

  String plantLabel(String id) {
    final plant = plantById(id);
    if (plant == null) return 'Unbekannte Pflanze';
    return '${plant.room} · ${plant.name}';
  }

  DateTime? lastActivityDate(String plantId, PlantActivityType type) {
    final values =
        activities
            .where(
              (activity) =>
                  activity.plantId == plantId && activity.type == type,
            )
            .map((activity) => DateTime.tryParse(activity.date))
            .whereType<DateTime>()
            .toList()
          ..sort((a, b) => b.compareTo(a));
    return values.firstOrNull;
  }

  bool isWaterDue(Plant plant, {DateTime? now}) {
    final lastWater = lastActivityDate(plant.id, PlantActivityType.water);
    if (lastWater == null) return true;
    final checkDate = now ?? DateTime.now();
    final nextWater = DateTime(
      lastWater.year,
      lastWater.month,
      lastWater.day + plant.waterPeriodDays,
    );
    return !nextWater.isAfter(checkDate);
  }

  bool isRepotDue(Plant plant, {DateTime? now}) {
    final lastRepot = lastActivityDate(plant.id, PlantActivityType.repot);
    if (lastRepot == null) return true;
    final checkDate = now ?? DateTime.now();
    final nextRepot = DateTime(
      lastRepot.year,
      lastRepot.month,
      lastRepot.day + 730,
    );
    return !nextRepot.isAfter(checkDate);
  }

  Future<void> addPlant({
    required String room,
    required String name,
    String latinName = '',
    int waterPeriodDays = 7,
    int waterVolumeMl = 250,
    String comment = '',
  }) async {
    final nextSort = plantsForRoom(room).isEmpty
        ? 10
        : plantsForRoom(room).last.sortOrder + 10;
    final plant = Plant(
      id: 'plant_${DateTime.now().millisecondsSinceEpoch}',
      room: room.trim(),
      name: name.trim(),
      sortOrder: nextSort,
      latinName: latinName.trim(),
      waterPeriodDays: waterPeriodDays,
      waterVolumeMl: waterVolumeMl,
      comment: comment.trim(),
    );

    plants = [...plants, plant];
    _sortPlants();
    await _repository.savePlants(plants);
    notifyListeners();
  }

  Future<void> archivePlant(String plantId) async {
    final plant = plantById(plantId);
    if (plant == null) return;
    final archived = plant.copyWith(
      room: '☠️',
      comment: plant.comment.isEmpty
          ? 'Todestag: ${todayIso()}'
          : '${plant.comment} | ${plant.room} | Todestag: ${todayIso()}',
    );
    plants = plants
        .map((item) => item.id == plantId ? archived : item)
        .toList();
    _sortPlants();
    await _repository.savePlants(plants);
    notifyListeners();
  }

  Future<void> logActivity({
    required String plantId,
    required PlantActivityType type,
    String comment = '',
    DateTime? date,
  }) async {
    final activity = PlantActivity(
      id: 'activity_${DateTime.now().millisecondsSinceEpoch}',
      date: _formatDate(date ?? DateTime.now()),
      plantId: plantId,
      type: type,
      comment: comment.trim(),
    );

    activities = [activity, ...activities];
    _sortActivities();
    await _repository.saveActivities(activities);
    notifyListeners();
  }

  Future<void> waterPlant(Plant plant, {int? volumeMl}) async {
    await logActivity(
      plantId: plant.id,
      type: PlantActivityType.water,
      comment: '${volumeMl ?? plant.waterVolumeMl} ml',
    );
  }

  Future<void> fertilisePlant(Plant plant, {String comment = ''}) async {
    await logActivity(
      plantId: plant.id,
      type: PlantActivityType.fertilise,
      comment: comment,
    );
  }

  Future<void> repotPlant(Plant plant, {String comment = ''}) async {
    await logActivity(
      plantId: plant.id,
      type: PlantActivityType.repot,
      comment: comment,
    );
  }

  Future<int> fertiliseAllActive({String comment = ''}) async {
    var count = 0;
    for (final plant in activePlants) {
      await logActivity(
        plantId: plant.id,
        type: PlantActivityType.fertilise,
        comment: comment,
      );
      count++;
    }
    return count;
  }

  void _sortPlants() {
    plants.sort((a, b) {
      final roomCompare = a.room.compareTo(b.room);
      if (roomCompare != 0) return roomCompare;
      return a.sortOrder.compareTo(b.sortOrder);
    });
  }

  void _sortActivities() {
    activities.sort((a, b) => b.date.compareTo(a.date));
  }

  String _formatDate(DateTime date) {
    final year = date.year.toString().padLeft(4, '0');
    final month = date.month.toString().padLeft(2, '0');
    final day = date.day.toString().padLeft(2, '0');
    return '$year-$month-$day';
  }
}

extension IterableX<T> on Iterable<T> {
  T? get firstOrNull => isEmpty ? null : first;

  T? firstWhereOrNull(bool Function(T element) test) {
    try {
      return firstWhere(test);
    } catch (_) {
      return null;
    }
  }
}
