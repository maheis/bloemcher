import 'package:flutter/material.dart';

import '../app_controller.dart';
import '../models.dart';

class ActivitiesPage extends StatelessWidget {
  const ActivitiesPage({super.key, required this.controller});

  final AppController controller;

  @override
  Widget build(BuildContext context) {
    final activities = controller.activities;

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Text('Aktivitäten', style: Theme.of(context).textTheme.titleLarge),
        const SizedBox(height: 12),
        if (activities.isEmpty)
          const Card(
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Text('Noch keine Pflegeaktivitäten.'),
            ),
          )
        else
          ...activities.map((activity) {
            return Card(
              child: ListTile(
                leading: Icon(
                  _iconFor(activity.type),
                  color: _colorFor(activity.type),
                ),
                title: Text(activity.type.label),
                subtitle: Text(
                  '${controller.plantLabel(activity.plantId)} · ${shortDate(activity.date)}',
                ),
                trailing: activity.comment.isEmpty
                    ? null
                    : Text(activity.comment),
              ),
            );
          }),
      ],
    );
  }

  IconData _iconFor(PlantActivityType type) {
    return switch (type) {
      PlantActivityType.water => Icons.water_drop,
      PlantActivityType.fertilise => Icons.spa,
      PlantActivityType.repot => Icons.yard,
    };
  }

  Color _colorFor(PlantActivityType type) {
    return switch (type) {
      PlantActivityType.water => Colors.blue,
      PlantActivityType.fertilise => Colors.green,
      PlantActivityType.repot => Colors.brown,
    };
  }
}
