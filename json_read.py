import json
import urllib
from pprint import pprint

def increment(waterpoints, key):
    waterpoints[key] = 1 if waterpoints.get(key, "null") == "null" else waterpoints[key] + 1
    return waterpoints;

def get_community_ranking(all_water_points, broken_water_points):
    percentage = {}
    for village, total in all_water_points.items():
        output = 0.0 if broken_water_points.get(village, "null") == "null" else round((float(broken_water_points.get(village)) / float(total)) * 100, 1)
        percentage[village] = output 
    percentage = sorted(percentage.items(), key=lambda t: t[1], reverse=True)
    return percentage;

def list_sum(waterpoints):
    return reduce(lambda x,y:x+y, [x for x in waterpoints])
    
def calculate2(url):
    broken_water_points, all_water_points, = {}, {}
    data = json.load(urllib.urlopen(url))
    for row in data:
        communities_villages, water_functioning = row['communities_villages'], row['water_functioning']
        if water_functioning != "yes": broken_water_points = increment(broken_water_points, communities_villages)
        all_water_points = increment(all_water_points, communities_villages)
    output = {
        "number_functional_waterpoints" : list_sum(all_water_points.values()) - list_sum(broken_water_points.values()),
        "all_water_points" : all_water_points,
        "community_ranking" : get_community_ranking(all_water_points, broken_water_points)
    }
    pprint(output)

calculate2("https://raw.githubusercontent.com/onaio/ona-tech/master/data/water_points.json")
