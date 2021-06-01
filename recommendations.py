import pandas as pd
log = pd.read_csv('log.txt', header=None, names=['ip', 'query', 'datetime'])
queries_by_user = log.groupby(['ip'])['query']
user_query = "coronavirus"
similar_queries = []
for ip, queries in queries_by_user:
    queries = queries.unique()
    if user_query in queries:
        for query in queries:
            if query != user_query:
                similar_queries.append(query)
similar_queries = pd.Series(similar_queries)
similar_queries = similar_queries.value_counts().index.tolist()
print(similar_queries[0:5])
