.PHONY: configure install start

# Règles obligatoires

configure: install
	# Configure app

install:
	# Install app dependencies (composer, npm, etc)

start:
	# Start service
	# Dependencies are declared by adding a DEPENDENCIES= string at the end of the command
	# eg. $(MAKE) -C ../../../.. vanillaminer-start-service DEPENDENCIES="rabbitmq ws"
	$(MAKE) -C ../../../.. vanillaminer-start-service DEPENDENCIES="dbgp-proxy musiquesincongrues"

# Règles propres au projet

# Vous pouvez définir des règles supplémentaires, propre au cycle de vie du projet
# Lors du développement, ces règles pourront être exécutées dans le contexte d'un container via
# la règle `<service>-make` : https://github.com/ARAMISAUTO/developer-portal/blob/master/README.md#service-make
init-db:
	./symfony doctrine:drop-db --no-confirmation
	./symfony doctrine:build-db
	./symfony doctrine:build --all-classes
	mysql -hmusiquesincongrues_mysql_vanilla ci_mi_miner -uroot -proot < data/sql/schema.sql
	mysql -hmusiquesincongrues_mysql_vanilla ci_mi_miner -uroot -proot < data/sql/init_migrations.sql
	mysql -hmusiquesincongrues_mysql_vanilla ci_mi_miner -uroot -proot < data/sql/init_extraction_log.sql

extract-links:
	php -d memory_limit=-1 ./symfony miner:extract-links mysql://root:root@musiquesincongrues_mysql_vanilla/vanilla

solr-index-db:
	php -d memory_limit=-1 ./symfony lucene:update-model frontend IndexA fr Link