#!/bin/bash
echo "clearing existing data if any from elasticsearch..."
echo ""
docker exec -it elasticsearch.local curl -XDELETE 'localhost:9200/dohmh'
echo ""
echo "running indexing of data..."
echo ""
docker exec -it logstash.local bash -c "cd /files && chmod a+x reindex-data.sh && ./reindex-data.sh"
echo ""
echo "...done."