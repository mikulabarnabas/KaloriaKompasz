package hu.kaloriakompasz

import android.content.Intent
import android.os.Bundle
import android.util.Log
import androidx.health.connect.client.HealthConnectClient
import androidx.health.connect.client.records.StepsRecord
import androidx.health.connect.client.request.ReadRecordsRequest
import androidx.health.connect.client.time.TimeRangeFilter
import androidx.lifecycle.lifecycleScope
import com.getcapacitor.BridgeActivity
import com.getcapacitor.JSObject
import com.getcapacitor.Plugin
import com.getcapacitor.PluginCall
import com.getcapacitor.PluginMethod
import com.getcapacitor.annotation.CapacitorPlugin
import kotlinx.coroutines.launch
import java.time.Instant
import java.time.ZoneId

import com.samsung.android.sdk.health.data.HealthDataService
import com.samsung.android.sdk.health.data.HealthDataStore
import com.samsung.android.sdk.health.data.permission.Permission
import com.samsung.android.sdk.health.data.permission.AccessType
import com.samsung.android.sdk.health.data.request.AggregateRequest
import com.samsung.android.sdk.health.data.request.LocalTimeFilter
import com.samsung.android.sdk.health.data.request.LocalTimeGroup
import com.samsung.android.sdk.health.data.request.LocalTimeGroupUnit
import com.samsung.android.sdk.health.data.response.DataResponse
import com.samsung.android.sdk.health.data.data.AggregatedData
import com.samsung.android.sdk.health.data.request.DataType
import com.samsung.android.sdk.health.data.request.DataTypes
import com.samsung.android.sdk.health.data.error.HealthDataException

import java.time.LocalDate
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class MainActivity : BridgeActivity() {

    public override fun onCreate(savedInstanceState: Bundle?) {
        registerPlugin(HealthConnectBridgePlugin::class.java)
        registerPlugin(SamsungHealthPlugin::class.java)
        super.onCreate(savedInstanceState)
    }

    @CapacitorPlugin(name = "HealthConnectBridge")
    open class HealthConnectBridgePlugin : Plugin() {

        private val healthConnectClient by lazy { HealthConnectClient.getOrCreate(context) }

        @PluginMethod
        fun getHealthStatus(call: PluginCall) {
            val availabilityStatus = HealthConnectClient.getSdkStatus(context)
            val ret = JSObject()
            ret.put("status", availabilityStatus)

            if (availabilityStatus == 3) {
                val providerPackageName = "com.google.android.apps.healthdata"
                ret.put("updateUri", "market://details?id=$providerPackageName")
            }
            call.resolve(ret)
        }

        @PluginMethod
        fun openHealthSettings(call: PluginCall) {
            try {
                val intent = Intent("android.settings.HEALTH_CONNECT_SETTINGS")
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                context.startActivity(intent)
                call.resolve()
            } catch (e: Exception) {
                call.reject("Nem sikerült megnyitni: ${e.message}")
            }
        }

        @PluginMethod
        fun getSteps(call: PluginCall) {
            // Indítunk egy Coroutine-t a háttérben
            activity.lifecycleScope.launch {
                try {
                    val startOfDay = Instant.now()
                        .atZone(ZoneId.systemDefault())
                        .toLocalDate()
                        .atStartOfDay(ZoneId.systemDefault())
                        .toInstant()

                    val endTime = Instant.now()

                    val response = healthConnectClient.readRecords(
                        ReadRecordsRequest(
                            recordType = StepsRecord::class,
                            timeRangeFilter = TimeRangeFilter.between(startOfDay, endTime)
                        )
                    )

                    val totalSteps = response.records.sumOf { it.count }

                    val ret = JSObject()
                    ret.put("steps", totalSteps)
                    call.resolve(ret)
                } catch (e: Exception) {
                    call.reject("Hiba a lépések lekérdezésekor: ${e.message}")
                }
            }
        }

    }

    @CapacitorPlugin(name = "SamsungHealthCustom")
    open class SamsungHealthPlugin : com.getcapacitor.Plugin() {

        @PluginMethod
        fun getSamsungSteps(call: PluginCall) {
            val date = LocalDate.now()

            // Használjuk a beépített lifecycleScope-ot a CoroutineScope helyett!
            activity.lifecycleScope.launch {
                try {
                    val store = HealthDataService.getStore(context)
                    val requiredPermissions = setOf(Permission.of(DataTypes.STEPS, AccessType.READ))

                    // 1. Engedélyek ellenőrzése és kérése
                    val granted = areAllPermissionsObtained(store, requiredPermissions)

                    if (granted) {
                        // 2. Adatlekérés (külön IO szálon, hogy ne blokkolja a UI-t)
                        val totalSteps = withContext(Dispatchers.IO) {
                            val stepsRequest = DataType.StepsType.TOTAL.requestBuilder
                                .setLocalTimeFilterWithGroup(
                                    LocalTimeFilter.of(date.atStartOfDay(), date.plusDays(1).atStartOfDay()),
                                    LocalTimeGroup.of(LocalTimeGroupUnit.DAILY, 1)
                                ).build()

                            val response = store.aggregateData(stepsRequest)
                            var sum = 0L
                            response.dataList.forEach { sum += it.value ?: 0L }
                            sum
                        }

                        val ret = JSObject()
                        ret.put("steps", totalSteps)
                        call.resolve(ret)
                    } else {
                        call.reject("A felhasználó megtagadta az engedélyeket.")
                    }
                } catch (e: Exception) {
                    Log.e("SamsungHealth", "Hiba", e)
                    call.reject("Kritikus hiba: ${e.localizedMessage}")
                }
            }
        }

        private suspend fun areAllPermissionsObtained(
            store: HealthDataStore,
            permissions: Set<Permission>
        ): Boolean {
            return try {
                val initialResult = store.getGrantedPermissions(permissions)
                if (!initialResult.containsAll(permissions)) {
                    // Itt történik a "mágia": az activity.lifecycleScope biztosítja,
                    // hogy az app ne záródjon be, amíg az új ablak megnyílik.
                    val obtainedResult = store.requestPermissions(permissions, activity)
                    obtainedResult.containsAll(permissions)
                } else {
                    true
                }
            } catch (error: HealthDataException) {
                Log.e("SamsungHealth", "Engedély hiba", error)
                false
            }
        }

        private suspend fun getAggregateResult(
            store: HealthDataStore,
            date: LocalDate
        ): DataResponse<AggregatedData<Long>> {
            val stepsRequest = DataType.StepsType.TOTAL.requestBuilder
                .setLocalTimeFilterWithGroup(
                    LocalTimeFilter.of(date.atStartOfDay(), date.plusDays(1).atStartOfDay()),
                    LocalTimeGroup.of(LocalTimeGroupUnit.DAILY, 1)
                ).build()

            return store.aggregateData(stepsRequest)
        }
    }
}
