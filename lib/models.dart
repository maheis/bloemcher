import 'package:intl/intl.dart';

enum PlantActivityType {
  water('Gießen'),
  fertilise('Düngen'),
  repot('Umtopfen');

  const PlantActivityType(this.label);

  final String label;

  static PlantActivityType fromStorage(String value) {
    return PlantActivityType.values.firstWhere(
      (type) => type.name == value || type.label == value,
      orElse: () => PlantActivityType.water,
    );
  }
}

class Plant {
  const Plant({
    required this.id,
    required this.room,
    required this.name,
    required this.sortOrder,
    this.latinName = '',
    this.waterPeriodDays = 7,
    this.waterVolumeMl = 250,
    this.comment = '',
  });

  final String id;
  final String room;
  final String name;
  final int sortOrder;
  final String latinName;
  final int waterPeriodDays;
  final int waterVolumeMl;
  final String comment;

  bool get isArchived => room == '☠️';

  Plant copyWith({
    String? id,
    String? room,
    String? name,
    int? sortOrder,
    String? latinName,
    int? waterPeriodDays,
    int? waterVolumeMl,
    String? comment,
  }) {
    return Plant(
      id: id ?? this.id,
      room: room ?? this.room,
      name: name ?? this.name,
      sortOrder: sortOrder ?? this.sortOrder,
      latinName: latinName ?? this.latinName,
      waterPeriodDays: waterPeriodDays ?? this.waterPeriodDays,
      waterVolumeMl: waterVolumeMl ?? this.waterVolumeMl,
      comment: comment ?? this.comment,
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'room': room,
    'name': name,
    'sortOrder': sortOrder,
    'latinName': latinName,
    'waterPeriodDays': waterPeriodDays,
    'waterVolumeMl': waterVolumeMl,
    'comment': comment,
  };

  factory Plant.fromJson(Map<String, dynamic> json) {
    return Plant(
      id: (json['id'] ?? '').toString(),
      room: (json['room'] ?? '').toString(),
      name: (json['name'] ?? '').toString(),
      sortOrder: (json['sortOrder'] as num?)?.toInt() ?? 0,
      latinName: (json['latinName'] ?? '').toString(),
      waterPeriodDays: (json['waterPeriodDays'] as num?)?.toInt() ?? 7,
      waterVolumeMl: (json['waterVolumeMl'] as num?)?.toInt() ?? 250,
      comment: (json['comment'] ?? '').toString(),
    );
  }
}

class PlantActivity {
  const PlantActivity({
    required this.id,
    required this.date,
    required this.plantId,
    required this.type,
    this.comment = '',
  });

  final String id;
  final String date;
  final String plantId;
  final PlantActivityType type;
  final String comment;

  Map<String, dynamic> toJson() => {
    'id': id,
    'date': date,
    'plantId': plantId,
    'type': type.name,
    'comment': comment,
  };

  factory PlantActivity.fromJson(Map<String, dynamic> json) {
    return PlantActivity(
      id: (json['id'] ?? '').toString(),
      date: (json['date'] ?? todayIso()).toString(),
      plantId: (json['plantId'] ?? '').toString(),
      type: PlantActivityType.fromStorage((json['type'] ?? '').toString()),
      comment: (json['comment'] ?? '').toString(),
    );
  }
}

String todayIso() => DateFormat('yyyy-MM-dd').format(DateTime.now());

String shortDate(String isoDate) {
  final date = DateTime.tryParse(isoDate);
  if (date == null) return isoDate;
  return DateFormat('dd.MM.yyyy').format(date);
}
