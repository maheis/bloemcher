import 'package:flutter/material.dart';

import '../app_controller.dart';

class PlantEditorPage extends StatefulWidget {
  const PlantEditorPage({super.key, required this.controller});

  final AppController controller;

  @override
  State<PlantEditorPage> createState() => _PlantEditorPageState();
}

class _PlantEditorPageState extends State<PlantEditorPage> {
  final _roomController = TextEditingController();
  final _nameController = TextEditingController();
  final _latinNameController = TextEditingController();
  final _waterPeriodController = TextEditingController(text: '7');
  final _waterVolumeController = TextEditingController(text: '250');
  final _commentController = TextEditingController();

  Future<void> _save() async {
    final room = _roomController.text.trim();
    final name = _nameController.text.trim();
    final waterPeriod = int.tryParse(_waterPeriodController.text.trim()) ?? 7;
    final waterVolume = int.tryParse(_waterVolumeController.text.trim()) ?? 250;

    if (room.isEmpty || name.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Bitte Raum und Pflanzenname eingeben.')),
      );
      return;
    }

    await widget.controller.addPlant(
      room: room,
      name: name,
      latinName: _latinNameController.text,
      waterPeriodDays: waterPeriod,
      waterVolumeMl: waterVolume,
      comment: _commentController.text,
    );

    if (!mounted) return;
    Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pflanze anlegen')),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            TextField(
              controller: _roomController,
              decoration: const InputDecoration(labelText: 'Raum'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _nameController,
              decoration: const InputDecoration(labelText: 'Pflanze'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _latinNameController,
              decoration: const InputDecoration(labelText: 'Lateinischer Name'),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _waterPeriodController,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Gießperiode'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: TextField(
                    controller: _waterVolumeController,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Menge ml'),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _commentController,
              decoration: const InputDecoration(labelText: 'Kommentar'),
              minLines: 2,
              maxLines: 4,
            ),
            const SizedBox(height: 24),
            FilledButton.icon(
              onPressed: _save,
              icon: const Icon(Icons.local_florist),
              label: const Text('Pflanze speichern'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _roomController.dispose();
    _nameController.dispose();
    _latinNameController.dispose();
    _waterPeriodController.dispose();
    _waterVolumeController.dispose();
    _commentController.dispose();
    super.dispose();
  }
}
