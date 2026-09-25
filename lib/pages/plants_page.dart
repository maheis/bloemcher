import 'package:flutter/material.dart';

import '../app_controller.dart';
import '../models.dart';

class PlantsPage extends StatefulWidget {
  const PlantsPage({super.key, required this.controller});

  final AppController controller;

  @override
  State<PlantsPage> createState() => _PlantsPageState();
}

class _PlantsPageState extends State<PlantsPage> {
  String _selectedRoom = 'Alle';

  @override
  Widget build(BuildContext context) {
    final rooms = ['Alle', ...widget.controller.rooms];
    if (!rooms.contains(_selectedRoom)) _selectedRoom = 'Alle';
    final plants = widget.controller.plantsForRoom(_selectedRoom);

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        DropdownButtonFormField<String>(
          initialValue: _selectedRoom,
          decoration: const InputDecoration(labelText: 'Raum'),
          items: rooms
              .map((room) => DropdownMenuItem(value: room, child: Text(room)))
              .toList(),
          onChanged: (value) => setState(() => _selectedRoom = value ?? 'Alle'),
        ),
        const SizedBox(height: 16),
        ...plants.map(
          (plant) => _PlantCard(controller: widget.controller, plant: plant),
        ),
      ],
    );
  }
}

class _PlantCard extends StatelessWidget {
  const _PlantCard({required this.controller, required this.plant});

  final AppController controller;
  final Plant plant;

  @override
  Widget build(BuildContext context) {
    final waterDue = controller.isWaterDue(plant);
    final repotDue = controller.isRepotDue(plant);
    final lastWater = controller.lastActivityDate(
      plant.id,
      PlantActivityType.water,
    );

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  Icons.local_florist,
                  color: waterDue ? Colors.blue : Colors.green,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        plant.name,
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                      if (plant.latinName.isNotEmpty) Text(plant.latinName),
                    ],
                  ),
                ),
                IconButton(
                  tooltip: 'Archivieren',
                  onPressed: () => controller.archivePlant(plant.id),
                  icon: const Icon(Icons.delete_outline),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                Chip(label: Text(plant.room)),
                Chip(label: Text('${plant.waterPeriodDays} Tage')),
                Chip(label: Text('${plant.waterVolumeMl} ml')),
                if (waterDue) const Chip(label: Text('Gießen fällig')),
                if (repotDue) const Chip(label: Text('Umtopfen prüfen')),
              ],
            ),
            const SizedBox(height: 10),
            Text(
              'Letztes Gießen: ${lastWater == null ? 'nie' : shortDate(lastWater.toIso8601String())}',
            ),
            if (plant.comment.isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(plant.comment),
            ],
            const SizedBox(height: 12),
            Wrap(
              spacing: 8,
              children: [
                FilledButton.tonalIcon(
                  onPressed: () => controller.waterPlant(plant),
                  icon: const Icon(Icons.water_drop),
                  label: const Text('Gießen'),
                ),
                FilledButton.tonalIcon(
                  onPressed: () => controller.fertilisePlant(plant),
                  icon: const Icon(Icons.spa),
                  label: const Text('Düngen'),
                ),
                FilledButton.tonalIcon(
                  onPressed: () => controller.repotPlant(plant),
                  icon: const Icon(Icons.yard),
                  label: const Text('Umtopfen'),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
