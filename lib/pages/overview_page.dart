import 'package:flutter/material.dart';

import '../app_controller.dart';
import '../models.dart';

class OverviewPage extends StatelessWidget {
  const OverviewPage({super.key, required this.controller});

  final AppController controller;

  @override
  Widget build(BuildContext context) {
    final duePlants = controller.dueWaterPlants();
    final totalWater = controller.dueWaterVolumeMl();

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Row(
          children: [
            Expanded(
              child: _SummaryCard(
                title: 'Fällig',
                value: duePlants.length.toString(),
                subtitle: 'Pflanzen gießen',
                icon: Icons.water_drop,
                color: Colors.blue,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _SummaryCard(
                title: 'Wasser',
                value: '$totalWater ml',
                subtitle: 'heute geplant',
                icon: Icons.local_drink,
                color: Colors.teal,
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Text('Heute dran', style: Theme.of(context).textTheme.titleLarge),
        const SizedBox(height: 12),
        if (duePlants.isEmpty)
          const Card(
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Text('Alles versorgt. Heute ist keine Pflanze fällig.'),
            ),
          )
        else
          ...duePlants.map((plant) {
            final lastWater = controller.lastActivityDate(
              plant.id,
              PlantActivityType.water,
            );
            return Card(
              child: ListTile(
                leading: const Icon(Icons.water_drop, color: Colors.blue),
                title: Text(plant.name),
                subtitle: Text(
                  '${plant.room} · ${plant.waterVolumeMl} ml · zuletzt ${lastWater == null ? 'nie' : shortDate(lastWater.toIso8601String())}',
                ),
                trailing: FilledButton.tonalIcon(
                  onPressed: () => controller.waterPlant(plant),
                  icon: const Icon(Icons.check),
                  label: const Text('Gießen'),
                ),
              ),
            );
          }),
        const SizedBox(height: 20),
        Text('Schnellpflege', style: Theme.of(context).textTheme.titleLarge),
        const SizedBox(height: 12),
        FilledButton.icon(
          onPressed: controller.activePlants.isEmpty
              ? null
              : () async {
                  final messenger = ScaffoldMessenger.of(context);
                  final count = await controller.fertiliseAllActive();
                  messenger.showSnackBar(
                    SnackBar(content: Text('$count Pflanzen gedüngt.')),
                  );
                },
          icon: const Icon(Icons.spa),
          label: const Text('Alle aktiven Pflanzen düngen'),
        ),
      ],
    );
  }
}

class _SummaryCard extends StatelessWidget {
  const _SummaryCard({
    required this.title,
    required this.value,
    required this.subtitle,
    required this.icon,
    required this.color,
  });

  final String title;
  final String value;
  final String subtitle;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Card(
      color: color.withAlpha(28),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color),
            const SizedBox(height: 12),
            Text(title, style: Theme.of(context).textTheme.labelLarge),
            const SizedBox(height: 6),
            FittedBox(
              fit: BoxFit.scaleDown,
              alignment: Alignment.centerLeft,
              child: Text(
                value,
                style: Theme.of(context).textTheme.headlineSmall
                    ?.copyWith(fontWeight: FontWeight.bold),
              ),
            ),
            const SizedBox(height: 4),
            Text(subtitle),
          ],
        ),
      ),
    );
  }
}
