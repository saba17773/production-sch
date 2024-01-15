# Item Master

```sql
INSERT ItemMaster(ID, NameTH, Pattern, Brand, UnitID, ItemGroup, InternalNumber, ProductGroup, SubGroup, QtyPerPallet)
SELECT
I.ITEMID, I.DSGThaiItemDescription ,  DSGPatternID, DSGBandID, I.BOMUNITID, I.ItemGroupId, I.DSG_InternalItemId, I.DSGProductGroupID, I.DSGSubGroupID, I.DSG_QtyPerPallet
FROM [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] I 
WHERE I.DSGProductGroupID IN ('TBR')
AND I.ITEMGROUPID IN ('FG', 'SM')
AND I.DSG_Obsolete = 0
AND I.ITEMID NOT IN (
	SELECT ID FROM ItemMaster
)
AND ITEMNAME <> 'ห้ามใช้'
AND ITEMNAME <> 'รอการตรวจสอบ'
OR I.DSGSubGroupID in ('SM0908')
GROUP BY I.ITEMID, I.DSGThaiItemDescription ,  DSGPatternID, DSGBandID, I.BOMUNITID, I.ItemGroupId, I.DSG_InternalItemId, I.DSGProductGroupID, I.DSGSubGroupID, I.DSG_QtyPerPallet

```

# Location
```sql
insert into Location(Description, LocationType)
select LL.wMSLocationId[location], 'sync' [sync] from [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[WMSLocation]  LL
where LL.INVENTLOCATIONID = 'STRFG1'
and LL.DSG_DisableLocation = 0
and LL.wMSLocationId NOT IN (
select Description from Location
)
group by LL.wMSLocationId
```