import 'dart:io';

import 'package:flutter/material.dart';
import 'package:path/path.dart' as p;
import 'package:path_provider/path_provider.dart';
import 'package:sembast/sembast_io.dart';

import 'app.dart';
import 'app_controller.dart';
import 'repository/app_repository.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  final directory = await getApplicationDocumentsDirectory();
  final dbDirectory = Directory(p.join(directory.path, 'bloemcher'));
  await dbDirectory.create(recursive: true);

  final database = await databaseFactoryIo.openDatabase(
    p.join(dbDirectory.path, 'bloemcher.db'),
  );

  final controller = AppController(LocalAppRepository(database));
  await controller.load();

  runApp(BloemcherApp(controller: controller));
}
