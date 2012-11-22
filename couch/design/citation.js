{
   "_id": "_design/citation",
   "language": "javascript",
   "indexes": {
       "all": {
           "index": "function(doc) { if (doc.citation) {  index(\"default\", doc.citation, {\"store\": \"yes\"}); } }"
       }
   }
}