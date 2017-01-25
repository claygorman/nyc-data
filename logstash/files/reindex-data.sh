#!/bin/bash
logstash -f /config-dir/logstash.conf < /files/dohmh-new-york-city-restaurant-inspection-results-1.csv
logstash -f /config-dir/logstash.conf < /files/dohmh-new-york-city-restaurant-inspection-results-2.csv
logstash -f /config-dir/logstash.conf < /files/dohmh-new-york-city-restaurant-inspection-results-3.csv
logstash -f /config-dir/logstash.conf < /files/dohmh-new-york-city-restaurant-inspection-results-4.csv
